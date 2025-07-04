<?php

namespace Staatic\Vendor\Symfony\Component\Config\Resource;

use IteratorAggregate;
use InvalidArgumentException;
use Traversable;
use SplFileInfo;
use RecursiveIteratorIterator;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use LogicException;
use Staatic\Vendor\Symfony\Component\Finder\Finder;
use Staatic\Vendor\Symfony\Component\Finder\Glob;
class GlobResource implements IteratorAggregate, SelfCheckingResourceInterface
{
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var string
     */
    private $pattern;
    /**
     * @var bool
     */
    private $recursive;
    /**
     * @var string
     */
    private $hash;
    /**
     * @var bool
     */
    private $forExclusion;
    /**
     * @var mixed[]
     */
    private $excludedPrefixes;
    /**
     * @var int
     */
    private $globBrace;
    public function __construct(string $prefix, string $pattern, bool $recursive, bool $forExclusion = \false, array $excludedPrefixes = [])
    {
        ksort($excludedPrefixes);
        $resolvedPrefix = realpath($prefix) ?: (file_exists($prefix) ? $prefix : \false);
        $this->pattern = $pattern;
        $this->recursive = $recursive;
        $this->forExclusion = $forExclusion;
        $this->excludedPrefixes = $excludedPrefixes;
        $this->globBrace = \defined('GLOB_BRACE') ? \GLOB_BRACE : 0;
        if (\false === $resolvedPrefix) {
            throw new InvalidArgumentException(sprintf('The path "%s" does not exist.', $prefix));
        }
        $this->prefix = $resolvedPrefix;
    }
    public function getPrefix(): string
    {
        return $this->prefix;
    }
    public function __toString(): string
    {
        return 'glob.' . $this->prefix . (int) $this->recursive . $this->pattern . (int) $this->forExclusion . implode("\x00", $this->excludedPrefixes);
    }
    /**
     * @param int $timestamp
     */
    public function isFresh($timestamp): bool
    {
        $hash = $this->computeHash();
        $this->hash = $this->hash ?? $hash;
        return $this->hash === $hash;
    }
    public function __sleep(): array
    {
        $this->hash = $this->hash ?? $this->computeHash();
        return ['prefix', 'pattern', 'recursive', 'hash', 'forExclusion', 'excludedPrefixes'];
    }
    public function __wakeup(): void
    {
        $this->globBrace = \defined('GLOB_BRACE') ? \GLOB_BRACE : 0;
    }
    public function getIterator(): Traversable
    {
        if (!$this->recursive && '' === $this->pattern || !file_exists($this->prefix)) {
            return;
        }
        if (is_file($prefix = str_replace('\\', '/', $this->prefix))) {
            $prefix = \dirname($prefix);
            $pattern = basename($prefix) . $this->pattern;
        } else {
            $pattern = $this->pattern;
        }
        if (class_exists(Finder::class)) {
            $regex = Glob::toRegex($pattern);
            if ($this->recursive) {
                $regex = substr_replace($regex, '(/|$)', -2, 1);
            }
        } else {
            $regex = null;
        }
        $prefixLen = \strlen($prefix);
        $paths = null;
        if ('' === $this->pattern && is_file($this->prefix)) {
            $paths = [$this->prefix => null];
        } elseif (strncmp($this->prefix, 'phar://', strlen('phar://')) !== 0 && (null !== $regex || strpos($this->pattern, '/**/') === false)) {
            if (strpos($this->pattern, '/**/') === false && ($this->globBrace || strpos($this->pattern, '{') === false)) {
                $paths = array_fill_keys(glob($this->prefix . $this->pattern, \GLOB_NOSORT | $this->globBrace), null);
            } elseif (strpos($this->pattern, '\\') === false || !preg_match('/\\\\[,{}]/', $this->pattern)) {
                $paths = [];
                foreach ($this->expandGlob($this->pattern) as $p) {
                    if (\false !== $i = strpos($p, '/**/')) {
                        $p = substr_replace($p, '/*', $i);
                    }
                    $paths += array_fill_keys(glob($this->prefix . $p, \GLOB_NOSORT), \false !== $i ? $regex : null);
                }
            }
        }
        if (null !== $paths) {
            uksort($paths, 'strnatcmp');
            foreach ($paths as $path => $regex) {
                if ($this->excludedPrefixes) {
                    $normalizedPath = str_replace('\\', '/', $path);
                    do {
                        if (isset($this->excludedPrefixes[$dirPath = $normalizedPath])) {
                            continue 2;
                        }
                    } while ($prefix !== $dirPath && $dirPath !== $normalizedPath = \dirname($dirPath));
                }
                if ((null === $regex || preg_match($regex, substr(str_replace('\\', '/', $path), $prefixLen))) && is_file($path)) {
                    yield $path => new SplFileInfo($path);
                }
                if (!is_dir($path)) {
                    continue;
                }
                if ($this->forExclusion && (null === $regex || preg_match($regex, substr(str_replace('\\', '/', $path), $prefixLen)))) {
                    yield $path => new SplFileInfo($path);
                    continue;
                }
                if (!($this->recursive || null !== $regex) || isset($this->excludedPrefixes[str_replace('\\', '/', $path)])) {
                    continue;
                }
                $files = iterator_to_array(new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS), function (SplFileInfo $file, $path) use ($regex, $prefixLen) {
                    return !isset($this->excludedPrefixes[$path = str_replace('\\', '/', $path)]) && (null === $regex || preg_match($regex, substr($path, $prefixLen)) || $file->isDir()) && '.' !== $file->getBasename()[0];
                }), RecursiveIteratorIterator::LEAVES_ONLY));
                uksort($files, 'strnatcmp');
                foreach ($files as $path => $info) {
                    if ($info->isFile()) {
                        yield $path => $info;
                    }
                }
            }
            return;
        }
        if (!class_exists(Finder::class)) {
            throw new LogicException('Extended glob patterns cannot be used as the Finder component is not installed. Try running "composer require symfony/finder".');
        }
        yield from (new Finder())->followLinks()->filter(function (SplFileInfo $info) use ($regex, $prefixLen, $prefix) {
            $normalizedPath = str_replace('\\', '/', $info->getPathname());
            if (!preg_match($regex, substr($normalizedPath, $prefixLen)) || !$info->isFile()) {
                return \false;
            }
            if ($this->excludedPrefixes) {
                do {
                    if (isset($this->excludedPrefixes[$dirPath = $normalizedPath])) {
                        return \false;
                    }
                } while ($prefix !== $dirPath && $dirPath !== $normalizedPath = \dirname($dirPath));
            }
        })->sortByName()->in($prefix);
    }
    private function computeHash(): string
    {
        $hash = hash_init('xxh128');
        foreach ($this->getIterator() as $path => $info) {
            hash_update($hash, $path . "\n");
        }
        return hash_final($hash);
    }
    private function expandGlob(string $pattern): array
    {
        $segments = preg_split('/\{([^{}]*+)\}/', $pattern, -1, \PREG_SPLIT_DELIM_CAPTURE);
        $paths = [$segments[0]];
        $patterns = [];
        for ($i = 1; $i < \count($segments); $i += 2) {
            $patterns = [];
            foreach (explode(',', $segments[$i]) as $s) {
                foreach ($paths as $p) {
                    $patterns[] = $p . $s . $segments[1 + $i];
                }
            }
            $paths = $patterns;
        }
        $j = 0;
        foreach ($patterns as $i => $p) {
            if (strpos($p, '{') !== false) {
                $p = $this->expandGlob($p);
                array_splice($paths, $i + $j, 1, $p);
                $j += \count($p) - 1;
            }
        }
        return $paths;
    }
}

<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use Traversable;
use ArrayIterator;
use BackedEnum;
use Countable;
use Closure;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\EnvNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ParameterCircularReferenceException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
class EnvVarProcessor implements EnvVarProcessorInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var Traversable
     */
    private $loaders;
    /**
     * @var mixed[]
     */
    private $loadedVars = [];
    public function __construct(ContainerInterface $container, Traversable $loaders = null)
    {
        $this->container = $container;
        $this->loaders = $loaders ?? new ArrayIterator();
    }
    public static function getProvidedTypes(): array
    {
        return ['base64' => 'string', 'bool' => 'bool', 'not' => 'bool', 'const' => 'bool|int|float|string|array', 'csv' => 'array', 'file' => 'string', 'float' => 'float', 'int' => 'int', 'json' => 'array', 'key' => 'bool|int|float|string|array', 'url' => 'array', 'query_string' => 'array', 'resolve' => 'string', 'default' => 'bool|int|float|string|array', 'string' => 'string', 'trim' => 'string', 'require' => 'bool|int|float|string|array', 'enum' => BackedEnum::class, 'shuffle' => 'array'];
    }
    /**
     * @param string $prefix
     * @param string $name
     * @param Closure $getEnv
     * @return mixed
     */
    public function getEnv($prefix, $name, $getEnv)
    {
        $i = strpos($name, ':');
        if ('key' === $prefix) {
            if (\false === $i) {
                throw new RuntimeException(sprintf('Invalid env "key:%s": a key specifier should be provided.', $name));
            }
            $next = substr($name, $i + 1);
            $key = substr($name, 0, $i);
            $array = $getEnv($next);
            if (!\is_array($array)) {
                throw new RuntimeException(sprintf('Resolved value of "%s" did not result in an array value.', $next));
            }
            if (!isset($array[$key]) && !\array_key_exists($key, $array)) {
                throw new EnvNotFoundException(sprintf('Key "%s" not found in %s (resolved from "%s").', $key, json_encode($array), $next));
            }
            return $array[$key];
        }
        if ('enum' === $prefix) {
            if (\false === $i) {
                throw new RuntimeException(sprintf('Invalid env "enum:%s": a "%s" class-string should be provided.', $name, BackedEnum::class));
            }
            $next = substr($name, $i + 1);
            $backedEnumClassName = substr($name, 0, $i);
            $backedEnumValue = $getEnv($next);
            if (!\is_string($backedEnumValue) && !\is_int($backedEnumValue)) {
                throw new RuntimeException(sprintf('Resolved value of "%s" did not result in a string or int value.', $next));
            }
            if (!is_subclass_of($backedEnumClassName, BackedEnum::class)) {
                throw new RuntimeException(sprintf('"%s" is not a "%s".', $backedEnumClassName, BackedEnum::class));
            }
            if ($backedEnumClassName::tryFrom($backedEnumValue) !== null) {
                throw new RuntimeException(sprintf('Enum value "%s" is not backed by "%s".', $backedEnumValue, $backedEnumClassName));
            }
            return $backedEnumClassName::tryFrom($backedEnumValue);
        }
        if ('default' === $prefix) {
            if (\false === $i) {
                throw new RuntimeException(sprintf('Invalid env "default:%s": a fallback parameter should be provided.', $name));
            }
            $next = substr($name, $i + 1);
            $default = substr($name, 0, $i);
            if ('' !== $default && !$this->container->hasParameter($default)) {
                throw new RuntimeException(sprintf('Invalid env fallback in "default:%s": parameter "%s" not found.', $name, $default));
            }
            try {
                $env = $getEnv($next);
                if ('' !== $env && null !== $env) {
                    return $env;
                }
            } catch (EnvNotFoundException $exception) {
            }
            return '' === $default ? null : $this->container->getParameter($default);
        }
        if ('file' === $prefix || 'require' === $prefix) {
            if (!\is_scalar($file = $getEnv($name))) {
                throw new RuntimeException(sprintf('Invalid file name: env var "%s" is non-scalar.', $name));
            }
            if (!is_file($file)) {
                throw new EnvNotFoundException(sprintf('File "%s" not found (resolved from "%s").', $file, $name));
            }
            if ('file' === $prefix) {
                return file_get_contents($file);
            } else {
                return require $file;
            }
        }
        $returnNull = \false;
        if ('' === $prefix) {
            $returnNull = \true;
            $prefix = 'string';
        }
        if (\false !== $i || 'string' !== $prefix) {
            $env = $getEnv($name);
        } elseif (isset($_ENV[$name])) {
            $env = $_ENV[$name];
        } elseif (isset($_SERVER[$name]) && strncmp($name, 'HTTP_', strlen('HTTP_')) !== 0) {
            $env = $_SERVER[$name];
        } elseif (\false === ($env = getenv($name)) || null === $env) {
            foreach ($this->loadedVars as $vars) {
                if (\false !== $env = $vars[$name] ?? \false) {
                    break;
                }
            }
            if (\false === $env || null === $env) {
                $loaders = $this->loaders;
                $this->loaders = new ArrayIterator();
                try {
                    $i = 0;
                    $ended = \true;
                    $count = $loaders instanceof Countable ? $loaders->count() : 0;
                    foreach ($loaders as $loader) {
                        if (\count($this->loadedVars) > $i++) {
                            continue;
                        }
                        $this->loadedVars[] = $vars = $loader->loadEnvVars();
                        if (\false !== $env = $vars[$name] ?? \false) {
                            $ended = \false;
                            break;
                        }
                    }
                    if ($ended || $count === $i) {
                        $loaders = $this->loaders;
                    }
                } catch (ParameterCircularReferenceException $exception) {
                } finally {
                    $this->loaders = $loaders;
                }
            }
            if (\false === $env || null === $env) {
                if (!$this->container->hasParameter("env({$name})")) {
                    throw new EnvNotFoundException(sprintf('Environment variable not found: "%s".', $name));
                }
                $env = $this->container->getParameter("env({$name})");
            }
        }
        if (null === $env) {
            if ($returnNull) {
                return null;
            }
            if (!isset($this->getProvidedTypes()[$prefix])) {
                throw new RuntimeException(sprintf('Unsupported env var prefix "%s".', $prefix));
            }
            if (!\in_array($prefix, ['string', 'bool', 'not', 'int', 'float'], \true)) {
                return null;
            }
        }
        if ('shuffle' === $prefix) {
            if (!\is_array($env)) {
                throw new RuntimeException(sprintf('Env var "%s" cannot be shuffled, expected array, got "%s".', $name, get_debug_type($env)));
            }
            return $env;
        }
        if (null !== $env && !\is_scalar($env)) {
            throw new RuntimeException(sprintf('Non-scalar env var "%s" cannot be cast to "%s".', $name, $prefix));
        }
        if ('string' === $prefix) {
            return (string) $env;
        }
        if (\in_array($prefix, ['bool', 'not'], \true)) {
            $env = (bool) ((filter_var($env, \FILTER_VALIDATE_BOOL) ?: filter_var($env, \FILTER_VALIDATE_INT)) ?: filter_var($env, \FILTER_VALIDATE_FLOAT));
            return 'not' === $prefix ? !$env : $env;
        }
        if ('int' === $prefix) {
            if (null !== $env && \false === $env = filter_var($env, \FILTER_VALIDATE_INT) ?: filter_var($env, \FILTER_VALIDATE_FLOAT)) {
                throw new RuntimeException(sprintf('Non-numeric env var "%s" cannot be cast to int.', $name));
            }
            return (int) $env;
        }
        if ('float' === $prefix) {
            if (null !== $env && \false === $env = filter_var($env, \FILTER_VALIDATE_FLOAT)) {
                throw new RuntimeException(sprintf('Non-numeric env var "%s" cannot be cast to float.', $name));
            }
            return (float) $env;
        }
        if ('const' === $prefix) {
            if (!\defined($env)) {
                throw new RuntimeException(sprintf('Env var "%s" maps to undefined constant "%s".', $name, $env));
            }
            return \constant($env);
        }
        if ('base64' === $prefix) {
            return base64_decode(strtr($env, '-_', '+/'));
        }
        if ('json' === $prefix) {
            $env = json_decode($env, \true);
            if (\JSON_ERROR_NONE !== json_last_error()) {
                throw new RuntimeException(sprintf('Invalid JSON in env var "%s": ', $name) . json_last_error_msg());
            }
            if (null !== $env && !\is_array($env)) {
                throw new RuntimeException(sprintf('Invalid JSON env var "%s": array or null expected, "%s" given.', $name, get_debug_type($env)));
            }
            return $env;
        }
        if ('url' === $prefix) {
            $parsedEnv = parse_url($env);
            if (\false === $parsedEnv) {
                throw new RuntimeException(sprintf('Invalid URL in env var "%s".', $name));
            }
            if (!isset($parsedEnv['scheme'], $parsedEnv['host'])) {
                throw new RuntimeException(sprintf('Invalid URL env var "%s": schema and host expected, "%s" given.', $name, $env));
            }
            $parsedEnv += ['port' => null, 'user' => null, 'pass' => null, 'path' => null, 'query' => null, 'fragment' => null];
            $parsedEnv['path'] = '/' === ($parsedEnv['path'] ?? '/') ? '' : substr($parsedEnv['path'], 1);
            return $parsedEnv;
        }
        if ('query_string' === $prefix) {
            $queryString = parse_url($env, \PHP_URL_QUERY) ?: $env;
            parse_str($queryString, $result);
            return $result;
        }
        if ('resolve' === $prefix) {
            return preg_replace_callback('/%%|%([^%\s]+)%/', function ($match) use ($name, $getEnv) {
                if (!isset($match[1])) {
                    return '%';
                }
                if (strncmp($match[1], 'env(', strlen('env(')) === 0 && substr_compare($match[1], ')', -strlen(')')) === 0 && 'env()' !== $match[1]) {
                    $value = $getEnv(substr($match[1], 4, -1));
                } else {
                    $value = $this->container->getParameter($match[1]);
                }
                if (!\is_scalar($value)) {
                    throw new RuntimeException(sprintf('Parameter "%s" found when resolving env var "%s" must be scalar, "%s" given.', $match[1], $name, get_debug_type($value)));
                }
                return $value;
            }, $env);
        }
        if ('csv' === $prefix) {
            return str_getcsv($env, ',', '"', '');
        }
        if ('trim' === $prefix) {
            return trim($env);
        }
        throw new RuntimeException(sprintf('Unsupported env var prefix "%s" for env name "%s".', $prefix, $name));
    }
}

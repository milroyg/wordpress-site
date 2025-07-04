<?php

namespace Staatic\Vendor\Symfony\Component\Config\Resource;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use ReflectionMethod;
use Staatic\Vendor\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Staatic\Vendor\Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ServiceSubscriberInterface;
class ReflectionClassResource implements SelfCheckingResourceInterface
{
    /**
     * @var mixed[]
     */
    private $files = [];
    /**
     * @var string
     */
    private $className;
    /**
     * @var ReflectionClass
     */
    private $classReflector;
    /**
     * @var mixed[]
     */
    private $excludedVendors = [];
    /**
     * @var string
     */
    private $hash;
    public function __construct(ReflectionClass $classReflector, array $excludedVendors = [])
    {
        $this->className = $classReflector->name;
        $this->classReflector = $classReflector;
        $this->excludedVendors = $excludedVendors;
    }
    /**
     * @param int $timestamp
     */
    public function isFresh($timestamp): bool
    {
        if (!isset($this->hash)) {
            $this->hash = $this->computeHash();
            $this->loadFiles($this->classReflector);
        }
        foreach ($this->files as $file => $v) {
            if (\false === $filemtime = @filemtime($file)) {
                return \false;
            }
            if ($filemtime > $timestamp) {
                return $this->hash === $this->computeHash();
            }
        }
        return \true;
    }
    public function __toString(): string
    {
        return 'reflection.' . $this->className;
    }
    public function __sleep(): array
    {
        if (!isset($this->hash)) {
            $this->hash = $this->computeHash();
            $this->loadFiles($this->classReflector);
        }
        return ['files', 'className', 'hash'];
    }
    private function loadFiles(ReflectionClass $class): void
    {
        foreach ($class->getInterfaces() as $v) {
            $this->loadFiles($v);
        }
        do {
            $file = $class->getFileName();
            if (\false !== $file && is_file($file)) {
                foreach ($this->excludedVendors as $vendor) {
                    if (strncmp($file, $vendor, strlen($vendor)) === 0 && \false !== strpbrk(substr($file, \strlen($vendor), 1), '/' . \DIRECTORY_SEPARATOR)) {
                        $file = \false;
                        break;
                    }
                }
                if ($file) {
                    $this->files[$file] = null;
                }
            }
            foreach ($class->getTraits() as $v) {
                $this->loadFiles($v);
            }
        } while ($class = $class->getParentClass());
    }
    private function computeHash(): string
    {
        try {
            $this->classReflector = $this->classReflector ?? new ReflectionClass($this->className);
        } catch (ReflectionException $exception) {
            return \false;
        }
        $hash = hash_init('xxh128');
        foreach ($this->generateSignature($this->classReflector) as $info) {
            hash_update($hash, $info);
        }
        return hash_final($hash);
    }
    private function generateSignature(ReflectionClass $class): iterable
    {
        $attributes = [];
        foreach (method_exists($class, 'getAttributes') ? $class->getAttributes() : [] as $a) {
            $attributes[] = [$a->getName(), (string) $a];
        }
        yield print_r($attributes, \true);
        $attributes = [];
        yield $class->getDocComment();
        yield (int) $class->isFinal();
        yield (int) $class->isAbstract();
        if ($class->isTrait()) {
            yield print_r(class_uses($class->name), \true);
        } else {
            yield print_r(class_parents($class->name), \true);
            yield print_r(class_implements($class->name), \true);
            yield print_r($class->getConstants(), \true);
        }
        if (!$class->isInterface()) {
            $defaults = $class->getDefaultProperties();
            foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED) as $p) {
                foreach (method_exists($p, 'getAttributes') ? $p->getAttributes() : [] as $a) {
                    $attributes[] = [$a->getName(), (string) $a];
                }
                yield print_r($attributes, \true);
                $attributes = [];
                yield $p->getDocComment();
                yield $p->isDefault() ? '<default>' : '';
                yield $p->isPublic() ? 'public' : 'protected';
                yield $p->isStatic() ? 'static' : '';
                yield '$' . $p->name;
                yield print_r(isset($defaults[$p->name]) && !\is_object($defaults[$p->name]) ? $defaults[$p->name] : null, \true);
            }
        }
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED) as $m) {
            foreach (method_exists($m, 'getAttributes') ? $m->getAttributes() : [] as $a) {
                $attributes[] = [$a->getName(), (string) $a];
            }
            yield print_r($attributes, \true);
            $attributes = [];
            $defaults = [];
            foreach ($m->getParameters() as $p) {
                foreach (method_exists($p, 'getAttributes') ? $p->getAttributes() : [] as $a) {
                    $attributes[] = [$a->getName(), (string) $a];
                }
                yield print_r($attributes, \true);
                $attributes = [];
                if (!$p->isDefaultValueAvailable()) {
                    $defaults[$p->name] = null;
                    continue;
                }
                $defaults[$p->name] = (string) $p;
            }
            yield preg_replace('/^  @@.*/m', '', $m);
            yield print_r($defaults, \true);
        }
        if ($class->isAbstract() || $class->isInterface() || $class->isTrait()) {
            return;
        }
        if (interface_exists(EventSubscriberInterface::class, \false) && $class->isSubclassOf(EventSubscriberInterface::class)) {
            yield EventSubscriberInterface::class;
            yield print_r($class->name::getSubscribedEvents(), \true);
        }
        if (interface_exists(MessageSubscriberInterface::class, \false) && $class->isSubclassOf(MessageSubscriberInterface::class)) {
            yield MessageSubscriberInterface::class;
            foreach ($class->name::getHandledMessages() as $key => $value) {
                yield $key . print_r($value, \true);
            }
        }
        if (interface_exists(ServiceSubscriberInterface::class, \false) && $class->isSubclassOf(ServiceSubscriberInterface::class)) {
            yield ServiceSubscriberInterface::class;
            yield print_r($class->name::getSubscribedServices(), \true);
        }
    }
}

<?php

namespace Staatic\Vendor\Symfony\Component\VarExporter\Internal;

use TypeError;
use Throwable;
use Closure;
use Staatic\Vendor\Symfony\Component\VarExporter\Hydrator as PublicHydrator;
class LazyObjectState
{
    /**
     * @readonly
     * @var Closure|mixed[]
     */
    public $initializer;
    public const STATUS_UNINITIALIZED_FULL = 1;
    public const STATUS_UNINITIALIZED_PARTIAL = 2;
    public const STATUS_INITIALIZED_FULL = 3;
    public const STATUS_INITIALIZED_PARTIAL = 4;
    /**
     * @readonly
     * @var mixed[]
     */
    public $skippedProperties;
    /**
     * @var int
     */
    public $status = 0;
    /**
     * @var object
     */
    public $realInstance;
    /**
     * @param Closure|mixed[] $initializer
     */
    public function __construct($initializer, $skippedProperties = [])
    {
        $this->initializer = $initializer;
        $this->skippedProperties = $skippedProperties;
        $this->status = \is_array($initializer) ? self::STATUS_UNINITIALIZED_PARTIAL : self::STATUS_UNINITIALIZED_FULL;
    }
    public function initialize($instance, $propertyName, $propertyScope)
    {
        if (self::STATUS_INITIALIZED_FULL === $this->status) {
            return self::STATUS_INITIALIZED_FULL;
        }
        if (\is_array($this->initializer)) {
            $class = get_class($instance);
            $propertyScope = $propertyScope ?? $class;
            $propertyScopes = Hydrator::$propertyScopes[$class];
            $propertyScopes[$k = "\x00{$propertyScope}\x00{$propertyName}"] ?? $propertyScopes[$k = "\x00*\x00{$propertyName}"] ?? $k = $propertyName;
            if ($initializer = $this->initializer[$k] ?? null) {
                $value = $initializer(...[$instance, $propertyName, $propertyScope, LazyObjectRegistry::$defaultProperties[$class][$k] ?? null]);
                $accessor = LazyObjectRegistry::$classAccessors[$propertyScope] = LazyObjectRegistry::$classAccessors[$propertyScope] ?? LazyObjectRegistry::getClassAccessors($propertyScope);
                $accessor['set']($instance, $propertyName, $value);
                return $this->status = self::STATUS_INITIALIZED_PARTIAL;
            }
            if ($initializer = $this->initializer["\x00"] ?? null) {
                if (!\is_array($values = $initializer($instance, LazyObjectRegistry::$defaultProperties[$class]))) {
                    throw new TypeError(sprintf('The lazy-initializer defined for instance of "%s" must return an array, got "%s".', $class, get_debug_type($values)));
                }
                $properties = (array) $instance;
                foreach ($values as $key => $value) {
                    if (!\array_key_exists($key, $properties) && [$scope, $name, $readonlyScope] = $propertyScopes[$key] ?? null) {
                        $scope = $readonlyScope ?? ('*' !== $scope ? $scope : $class);
                        $accessor = LazyObjectRegistry::$classAccessors[$scope] = LazyObjectRegistry::$classAccessors[$scope] ?? LazyObjectRegistry::getClassAccessors($scope);
                        $accessor['set']($instance, $name, $value);
                        if ($k === $key) {
                            $this->status = self::STATUS_INITIALIZED_PARTIAL;
                        }
                    }
                }
            }
            return $this->status;
        }
        if (self::STATUS_INITIALIZED_PARTIAL === $this->status) {
            return self::STATUS_INITIALIZED_PARTIAL;
        }
        $this->status = self::STATUS_INITIALIZED_PARTIAL;
        try {
            if ($defaultProperties = array_diff_key(LazyObjectRegistry::$defaultProperties[get_class($instance)], $this->skippedProperties)) {
                PublicHydrator::hydrate($instance, $defaultProperties);
            }
            ($this->initializer)($instance);
        } catch (Throwable $e) {
            $this->status = self::STATUS_UNINITIALIZED_FULL;
            $this->reset($instance);
            throw $e;
        }
        return $this->status = self::STATUS_INITIALIZED_FULL;
    }
    public function reset($instance): void
    {
        $class = get_class($instance);
        $propertyScopes = Hydrator::$propertyScopes[$class] = Hydrator::$propertyScopes[$class] ?? Hydrator::getPropertyScopes($class);
        $skippedProperties = $this->skippedProperties;
        $properties = (array) $instance;
        $onlyProperties = \is_array($this->initializer) ? $this->initializer : null;
        foreach ($propertyScopes as $key => [$scope, $name, $readonlyScope]) {
            $propertyScopes[$k = "\x00{$scope}\x00{$name}"] ?? $propertyScopes[$k = "\x00*\x00{$name}"] ?? $k = $name;
            if ($k === $key && (null !== $readonlyScope || !\array_key_exists($k, $properties))) {
                $skippedProperties[$k] = \true;
            }
        }
        foreach (LazyObjectRegistry::$classResetters[$class] as $reset) {
            $reset($instance, $skippedProperties, $onlyProperties);
        }
        $this->status = self::STATUS_INITIALIZED_FULL === $this->status ? self::STATUS_UNINITIALIZED_FULL : self::STATUS_UNINITIALIZED_PARTIAL;
    }
}

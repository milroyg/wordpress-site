<?php

namespace Staatic\Vendor\Symfony\Contracts\Service;

use ReflectionFunction;
use ReflectionNamedType;
use InvalidArgumentException;
use RuntimeException;
use Staatic\Vendor\Psr\Container\ContainerExceptionInterface;
use Staatic\Vendor\Psr\Container\NotFoundExceptionInterface;
class_exists(ContainerExceptionInterface::class);
class_exists(NotFoundExceptionInterface::class);
trait ServiceLocatorTrait
{
    /**
     * @var mixed[]
     */
    private $factories;
    /**
     * @var mixed[]
     */
    private $loading = [];
    /**
     * @var mixed[]
     */
    private $providedTypes;
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }
    /**
     * @param string $id
     */
    public function has($id): bool
    {
        return isset($this->factories[$id]);
    }
    /**
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        if (!isset($this->factories[$id])) {
            throw $this->createNotFoundException($id);
        }
        if (isset($this->loading[$id])) {
            $ids = array_values($this->loading);
            $ids = \array_slice($this->loading, array_search($id, $ids));
            $ids[] = $id;
            throw $this->createCircularReferenceException($id, $ids);
        }
        $this->loading[$id] = $id;
        try {
            return $this->factories[$id]($this);
        } finally {
            unset($this->loading[$id]);
        }
    }
    public function getProvidedServices(): array
    {
        if (!isset($this->providedTypes)) {
            $this->providedTypes = [];
            foreach ($this->factories as $name => $factory) {
                if (!\is_callable($factory)) {
                    $this->providedTypes[$name] = '?';
                } else {
                    $type = (new ReflectionFunction($factory))->getReturnType();
                    $this->providedTypes[$name] = $type ? ($type->allowsNull() ? '?' : '') . ($type instanceof ReflectionNamedType ? $type->getName() : $type) : '?';
                }
            }
        }
        return $this->providedTypes;
    }
    private function createNotFoundException(string $id): NotFoundExceptionInterface
    {
        if (!$alternatives = array_keys($this->factories)) {
            $message = 'is empty...';
        } else {
            $last = array_pop($alternatives);
            if ($alternatives) {
                $message = sprintf('only knows about the "%s" and "%s" services.', implode('", "', $alternatives), $last);
            } else {
                $message = sprintf('only knows about the "%s" service.', $last);
            }
        }
        if ($this->loading) {
            $message = sprintf('The service "%s" has a dependency on a non-existent service "%s". This locator %s', end($this->loading), $id, $message);
        } else {
            $message = sprintf('Service "%s" not found: the current service locator %s', $id, $message);
        }
        return new class($message) extends InvalidArgumentException implements NotFoundExceptionInterface
        {
        };
    }
    private function createCircularReferenceException(string $id, array $path): ContainerExceptionInterface
    {
        return new class(sprintf('Circular reference detected for service "%s", path: "%s".', $id, implode(' -> ', $path))) extends RuntimeException implements ContainerExceptionInterface
        {
        };
    }
}

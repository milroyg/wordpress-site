<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use Countable;
use ReflectionProperty;
use Staatic\Vendor\Psr\Container\ContainerExceptionInterface;
use Staatic\Vendor\Psr\Container\NotFoundExceptionInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Staatic\Vendor\Symfony\Contracts\Service\ServiceLocatorTrait;
use Staatic\Vendor\Symfony\Contracts\Service\ServiceProviderInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ServiceSubscriberInterface;
class ServiceLocator implements ServiceProviderInterface, Countable
{
    use ServiceLocatorTrait {
        get as private doGet;
    }
    /**
     * @var string|null
     */
    private $externalId;
    /**
     * @var Container|null
     */
    private $container;
    /**
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        if (!$this->externalId) {
            return $this->doGet($id);
        }
        try {
            return $this->doGet($id);
        } catch (RuntimeException $e) {
            $what = sprintf('service "%s" required by "%s"', $id, $this->externalId);
            $message = preg_replace('/service "\.service_locator\.[^"]++"/', $what, $e->getMessage());
            if ($e->getMessage() === $message) {
                $message = sprintf('Cannot resolve %s: %s', $what, $message);
            }
            $r = new ReflectionProperty($e, 'message');
            $r->setAccessible(true);
            $r->setValue($e, $message);
            throw $e;
        }
    }
    public function __invoke(string $id)
    {
        return isset($this->factories[$id]) ? $this->get($id) : null;
    }
    /**
     * @param string $externalId
     * @param Container $container
     * @return static
     */
    public function withContext($externalId, $container)
    {
        $locator = clone $this;
        $locator->externalId = $externalId;
        $locator->container = $container;
        return $locator;
    }
    public function count(): int
    {
        return \count($this->getProvidedServices());
    }
    private function createNotFoundException(string $id): NotFoundExceptionInterface
    {
        if ($this->loading) {
            $msg = sprintf('The service "%s" has a dependency on a non-existent service "%s". This locator %s', end($this->loading), $id, $this->formatAlternatives());
            return new ServiceNotFoundException($id, end($this->loading) ?: null, null, [], $msg);
        }
        $class = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS, 4);
        $class = isset($class[3]['object']) ? \get_class($class[3]['object']) : null;
        $externalId = $this->externalId ?: $class;
        $msg = [];
        $msg[] = sprintf('Service "%s" not found:', $id);
        if (!$this->container) {
            $class = null;
        } elseif ($this->container->has($id) || isset($this->container->getRemovedIds()[$id])) {
            $msg[] = 'even though it exists in the app\'s container,';
        } else {
            try {
                $this->container->get($id);
                $class = null;
            } catch (ServiceNotFoundException $e) {
                if ($e->getAlternatives()) {
                    $msg[] = sprintf('did you mean %s? Anyway,', $this->formatAlternatives($e->getAlternatives(), 'or'));
                } else {
                    $class = null;
                }
            }
        }
        if ($externalId) {
            $msg[] = sprintf('the container inside "%s" is a smaller service locator that %s', $externalId, $this->formatAlternatives());
        } else {
            $msg[] = sprintf('the current service locator %s', $this->formatAlternatives());
        }
        if (!$class) {
        } elseif (is_subclass_of($class, ServiceSubscriberInterface::class)) {
            $msg[] = sprintf('Unless you need extra laziness, try using dependency injection instead. Otherwise, you need to declare it using "%s::getSubscribedServices()".', preg_replace('/([^\\\\]++\\\\)++/', '', $class));
        } else {
            $msg[] = 'Try using dependency injection instead.';
        }
        return new ServiceNotFoundException($id, end($this->loading) ?: null, null, [], implode(' ', $msg));
    }
    private function createCircularReferenceException(string $id, array $path): ContainerExceptionInterface
    {
        return new ServiceCircularReferenceException($id, $path);
    }
    private function formatAlternatives(array $alternatives = null, string $separator = 'and'): string
    {
        $format = '"%s"%s';
        if (null === $alternatives) {
            if (!$alternatives = array_keys($this->factories)) {
                return 'is empty...';
            }
            $format = sprintf('only knows about the %s service%s.', $format, 1 < \count($alternatives) ? 's' : '');
        }
        $last = array_pop($alternatives);
        return sprintf($format, $alternatives ? implode('", "', $alternatives) : $last, $alternatives ? sprintf(' %s "%s"', $separator, $last) : '');
    }
}

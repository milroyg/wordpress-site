<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use ReflectionMethod;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
use Staatic\Vendor\Symfony\Component\VarExporter\ProxyHelper;
class ResolveNamedArgumentsPass extends AbstractRecursivePass
{
    /**
     * @param mixed $value
     * @param bool $isRoot
     * @return mixed
     */
    protected function processValue($value, $isRoot = \false)
    {
        if ($value instanceof AbstractArgument && $value->getText() . '.' === $value->getTextWithContext()) {
            $value->setContext(sprintf('A value found in service "%s"', $this->currentId));
        }
        if (!$value instanceof Definition) {
            return parent::processValue($value, $isRoot);
        }
        $calls = $value->getMethodCalls();
        $calls[] = ['__construct', $value->getArguments()];
        foreach ($calls as $i => $call) {
            [$method, $arguments] = $call;
            $parameters = null;
            $resolvedKeys = [];
            $resolvedArguments = [];
            foreach ($arguments as $key => $argument) {
                if ($argument instanceof AbstractArgument && $argument->getText() . '.' === $argument->getTextWithContext()) {
                    $argument->setContext(sprintf('Argument ' . (\is_int($key) ? 1 + $key : '"%3$s"') . ' of ' . ('__construct' === $method ? 'service "%s"' : 'method call "%s::%s()"'), $this->currentId, $method, $key));
                }
                if (\is_int($key)) {
                    $resolvedKeys[$key] = $key;
                    $resolvedArguments[$key] = $argument;
                    continue;
                }
                if (null === $parameters) {
                    $r = $this->getReflectionMethod($value, $method);
                    $class = $r instanceof ReflectionMethod ? $r->class : $this->currentId;
                    $method = $r->getName();
                    $parameters = $r->getParameters();
                }
                if (isset($key[0]) && '$' !== $key[0] && !class_exists($key) && !interface_exists($key, \false)) {
                    throw new InvalidArgumentException(sprintf('Invalid service "%s": did you forget to add the "$" prefix to argument "%s"?', $this->currentId, $key));
                }
                if (isset($key[0]) && '$' === $key[0]) {
                    foreach ($parameters as $j => $p) {
                        if ($key === '$' . $p->name) {
                            if ($p->isVariadic() && \is_array($argument)) {
                                foreach ($argument as $variadicArgument) {
                                    $resolvedKeys[$j] = $j;
                                    $resolvedArguments[$j++] = $variadicArgument;
                                }
                            } else {
                                $resolvedKeys[$j] = $p->name;
                                $resolvedArguments[$j] = $argument;
                            }
                            continue 2;
                        }
                    }
                    throw new InvalidArgumentException(sprintf('Invalid service "%s": method "%s()" has no argument named "%s". Check your service definition.', $this->currentId, $class !== $this->currentId ? $class . '::' . $method : $method, $key));
                }
                if (null !== $argument && !$argument instanceof Reference && !$argument instanceof Definition) {
                    throw new InvalidArgumentException(sprintf('Invalid service "%s": the value of argument "%s" of method "%s()" must be null, an instance of "%s" or an instance of "%s", "%s" given.', $this->currentId, $key, $class !== $this->currentId ? $class . '::' . $method : $method, Reference::class, Definition::class, get_debug_type($argument)));
                }
                $typeFound = \false;
                foreach ($parameters as $j => $p) {
                    if (!\array_key_exists($j, $resolvedArguments) && ProxyHelper::exportType($p, \true) === $key) {
                        $resolvedKeys[$j] = $p->name;
                        $resolvedArguments[$j] = $argument;
                        $typeFound = \true;
                    }
                }
                if (!$typeFound) {
                    throw new InvalidArgumentException(sprintf('Invalid service "%s": method "%s()" has no argument type-hinted as "%s". Check your service definition.', $this->currentId, $class !== $this->currentId ? $class . '::' . $method : $method, $key));
                }
            }
            if ($resolvedArguments !== $call[1]) {
                ksort($resolvedArguments);
                $arrayIsListFunction = function (array $array) : bool {
                    if (function_exists('array_is_list')) {
                        return array_is_list($array);
                    }
                    if ($array === []) {
                        return true;
                    }
                    $current_key = 0;
                    foreach ($array as $key => $noop) {
                        if ($key !== $current_key) {
                            return false;
                        }
                        ++$current_key;
                    }
                    return true;
                };
                if (!$value->isAutowired() && !$arrayIsListFunction($resolvedArguments)) {
                    ksort($resolvedKeys);
                    $resolvedArguments = array_combine($resolvedKeys, $resolvedArguments);
                }
                $calls[$i][1] = $resolvedArguments;
            }
        }
        [, $arguments] = array_pop($calls);
        if ($arguments !== $value->getArguments()) {
            $value->setArguments($arguments);
        }
        if ($calls !== $value->getMethodCalls()) {
            $value->setMethodCalls($calls);
        }
        foreach ($value->getProperties() as $key => $argument) {
            if ($argument instanceof AbstractArgument && $argument->getText() . '.' === $argument->getTextWithContext()) {
                $argument->setContext(sprintf('Property "%s" of service "%s"', $key, $this->currentId));
            }
        }
        return parent::processValue($value, $isRoot);
    }
}

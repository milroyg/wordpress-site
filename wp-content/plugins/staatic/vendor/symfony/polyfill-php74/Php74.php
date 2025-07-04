<?php

namespace Symfony\Polyfill\Php74;

use ArrayIterator;
use ArrayObject;
use ReflectionClass;

final class Php74
{
    public static function get_mangled_object_vars($obj)
    {
        if (!\is_object($obj)) {
            trigger_error('get_mangled_object_vars() expects parameter 1 to be object, ' . \gettype($obj) . ' given', \E_USER_WARNING);
            return null;
        }
        if ($obj instanceof ArrayIterator || $obj instanceof ArrayObject) {
            $reflector = new ReflectionClass($obj instanceof ArrayIterator ? 'ArrayIterator' : 'ArrayObject');
            $flags = $reflector->getMethod('getFlags')->invoke($obj);
            $reflector = $reflector->getMethod('setFlags');
            $reflector->invoke($obj, $flags & ArrayObject::STD_PROP_LIST ? 0 : ArrayObject::STD_PROP_LIST);
            $arr = (array) $obj;
            $reflector->invoke($obj, $flags);
        } else {
            $arr = (array) $obj;
        }
        return array_combine(array_keys($arr), array_values($arr));
    }
    public static function mb_str_split($string, $split_length = 1, $encoding = null)
    {
        if (null !== $string && !\is_scalar($string) && !(\is_object($string) && method_exists($string, '__toString'))) {
            trigger_error('mb_str_split() expects parameter 1 to be string, ' . \gettype($string) . ' given', \E_USER_WARNING);
            return null;
        }
        if (1 > $split_length = (int) $split_length) {
            trigger_error('The length of each segment must be greater than zero', \E_USER_WARNING);
            return \false;
        }
        if (null === $encoding) {
            $encoding = mb_internal_encoding();
        }
        if ('UTF-8' === $encoding || \in_array(strtoupper($encoding), ['UTF-8', 'UTF8'], \true)) {
            return preg_split("/(.{{$split_length}})/u", $string, -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);
        }
        $result = [];
        $length = mb_strlen($string, $encoding);
        for ($i = 0; $i < $length; $i += $split_length) {
            $result[] = mb_substr($string, $i, $split_length, $encoding);
        }
        return $result;
    }
    public static function password_algos()
    {
        $algos = [];
        if (\defined('PASSWORD_BCRYPT')) {
            $algos[] = \PASSWORD_BCRYPT;
        }
        if (\defined('PASSWORD_ARGON2I')) {
            $algos[] = \PASSWORD_ARGON2I;
        }
        if (\defined('PASSWORD_ARGON2ID')) {
            $algos[] = \PASSWORD_ARGON2ID;
        }
        return $algos;
    }
}

<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Exception;

use Throwable;
use Closure;

class AutowiringFailedException extends RuntimeException
{
    /**
     * @var string
     */
    private $serviceId;
    /**
     * @var Closure|null
     */
    private $messageCallback;
    /**
     * @param string|Closure $message
     */
    public function __construct(string $serviceId, $message = '', int $code = 0, Throwable $previous = null)
    {
        $this->serviceId = $serviceId;
        if ($message instanceof Closure && (\function_exists('xdebug_is_enabled') ? xdebug_is_enabled() : \function_exists('xdebug_info'))) {
            $message = $message();
        }
        if (!$message instanceof Closure) {
            parent::__construct($message, $code, $previous);
            return;
        }
        $this->messageCallback = $message;
        parent::__construct('', $code, $previous);
        $this->message = new class($this->message, $this->messageCallback)
        {
            /**
             * @var $this|string
             */
            private $message;
            /**
             * @var Closure|null
             */
            private $messageCallback;
            public function __construct(&$message, &$messageCallback)
            {
                $this->message =& $message;
                $this->messageCallback =& $messageCallback;
            }
            public function __toString(): string
            {
                $messageCallback = $this->messageCallback;
                $this->messageCallback = null;
                try {
                    return $this->message = $messageCallback();
                } catch (Throwable $e) {
                    return $this->message = $e->getMessage();
                }
            }
        };
    }
    public function getMessageCallback(): ?Closure
    {
        return $this->messageCallback;
    }
    public function getServiceId()
    {
        return $this->serviceId;
    }
}

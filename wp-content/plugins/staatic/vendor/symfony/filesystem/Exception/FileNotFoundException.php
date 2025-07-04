<?php

namespace Staatic\Vendor\Symfony\Component\Filesystem\Exception;

use Throwable;

class FileNotFoundException extends IOException
{
    public function __construct(?string $message = null, int $code = 0, ?Throwable $previous = null, ?string $path = null)
    {
        if (null === $message) {
            if (null === $path) {
                $message = 'File could not be found.';
            } else {
                $message = sprintf('File "%s" could not be found.', $path);
            }
        }
        parent::__construct($message, $code, $previous, $path);
    }
}

<?php

namespace Scaleplan\Access\Exceptions;

use Throwable;

/**
 * Class AccessException
 *
 * @package Scaleplan\Access\Exceptions
 */
class AccessException extends \Exception
{
    public const MESSAGE = 'Access error.';

    public function __construct(string $message = null, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?? static::MESSAGE, $code, $previous);
    }
}
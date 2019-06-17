<?php

namespace Scaleplan\Access\Exceptions;

/**
 * Class ValidationException
 *
 * @package Scaleplan\Access\Exceptions
 */
class ValidationException extends AccessException
{
    public const MESSAGE = 'Access validation error.';
    public const CODE = 422;
}

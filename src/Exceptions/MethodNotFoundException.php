<?php

namespace Scaleplan\Access\Exceptions;

/**
 * Class MethodNotFoundException
 *
 * @package Scaleplan\Access\Exceptions
 */
class MethodNotFoundException extends AccessException
{
    public const MESSAGE = 'Method not found.';
    public const CODE = 404;
}

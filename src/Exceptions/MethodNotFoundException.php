<?php

namespace Scaleplan\Access\Exceptions;

/**
 * Class AccessException
 *
 * @package Scaleplan\Access\Exceptions
 */
class MethodNotFoundException extends AccessException
{
    public const MESSAGE = 'Method not found.';
}
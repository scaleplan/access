<?php

namespace Scaleplan\Access\Exceptions;

/**
 * Class AccessDeniedException
 *
 * @package Scaleplan\Access\Exceptions
 */
class AccessDeniedException extends AccessException
{
    public const MESSAGE = 'Access denied.';
}
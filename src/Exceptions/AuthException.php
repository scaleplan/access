<?php

namespace Scaleplan\Access\Exceptions;

/**
 * Class AuthException
 *
 * @package Scaleplan\Access\Exceptions
 */
class AuthException extends AccessException
{
    public const MESSAGE = 'Please, log in.';
}
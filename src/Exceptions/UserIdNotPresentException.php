<?php

namespace Scaleplan\Access\Exceptions;

/**
 * Class UserIdNotPresentException
 *
 * @package Scaleplan\Access\Exceptions
 */
class UserIdNotPresentException extends AccessException
{
    public const MESSAGE = 'User ID not present.';
    public const CODE = 406;
}

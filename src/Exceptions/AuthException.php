<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

/**
 * Class AuthException
 *
 * @package Scaleplan\Access\Exceptions
 */
class AuthException extends AccessException
{
    public const MESSAGE = 'access.please-sign-in';
    public const CODE = 401;
}

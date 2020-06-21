<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

/**
 * Class UserIdNotPresentException
 *
 * @package Scaleplan\Access\Exceptions
 */
class UserIdNotPresentException extends AccessException
{
    public const MESSAGE = 'access.user-id-not-found';
    public const CODE = 406;
}

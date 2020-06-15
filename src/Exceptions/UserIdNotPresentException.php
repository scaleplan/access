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
    public const MESSAGE = 'Идентификатор пользователя не найден.';
    public const CODE = 406;
}

<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

/**
 * Class ValidationException
 *
 * @package Scaleplan\Access\Exceptions
 */
class ValidationException extends AccessException
{
    public const MESSAGE = 'Ошибка валидации.';
    public const CODE = 422;
}

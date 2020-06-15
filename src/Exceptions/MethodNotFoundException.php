<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

/**
 * Class MethodNotFoundException
 *
 * @package Scaleplan\Access\Exceptions
 */
class MethodNotFoundException extends AccessException
{
    public const MESSAGE = 'Метод не найден.';
    public const CODE = 404;
}

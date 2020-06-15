<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

/**
 * Class SupportingException
 *
 * @package Scaleplan\Access\Exceptions
 */
class SupportingException extends AccessException
{
    public const MESSAGE = 'Поддерживаюся только отражения методов и свойств.';
    public const CODE = 406;
}

<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

/**
 * Class FormatException
 *
 * @package Scaleplan\Access\Exceptions
 */
class FormatException extends AccessException
{
    public const MESSAGE = 'access.wrong-data-format';
    public const CODE = 406;
}

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
    public const MESSAGE = 'Allowed only reflection methods and reflection properties.';
    public const CODE = 406;
}

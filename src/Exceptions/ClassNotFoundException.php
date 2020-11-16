<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

/**
 * Class ClassNotFoundException
 *
 * @package Scaleplan\Access\Exceptions
 */
class ClassNotFoundException extends AccessException
{
    public const MESSAGE = 'access.class-not-found';
    public const CODE = 404;
}

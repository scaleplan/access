<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

/**
 * Class AccessDeniedException
 *
 * @package Scaleplan\Access\Exceptions
 */
class AccessDeniedException extends AccessException
{
    public const MESSAGE = 'Access denied.';
}

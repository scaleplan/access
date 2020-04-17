<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

/**
 * Class ConfigException
 *
 * @package Scaleplan\Access\Exceptions
 */
class ConfigException extends AccessException
{
    public const MESSAGE = 'Access configuring error.';
    public const CODE = 406;
}

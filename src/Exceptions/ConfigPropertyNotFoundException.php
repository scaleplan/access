<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

/**
 * Class ConfigPropertyNotFoundException
 *
 * @package Scaleplan\Access\Exceptions
 */
class ConfigPropertyNotFoundException extends ConfigException
{
    public const MESSAGE = 'access.config-setting-not-found';
}

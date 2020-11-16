<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

/**
 * Class CacheDataEmptyException
 *
 * @package Scaleplan\Access\Exceptions
 */
class CacheTypeNotSupportingException extends ConfigException
{
    public const MESSAGE = 'access.cache-type-not-supported';
}

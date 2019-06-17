<?php

namespace Scaleplan\Access\Exceptions;

/**
 * Class CacheDataEmptyException
 *
 * @package Scaleplan\Access\Exceptions
 */
class CacheTypeNotSupportingException extends ConfigException
{
    public const MESSAGE = 'Cache type not supporting.';
}

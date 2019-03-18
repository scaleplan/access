<?php

namespace Scaleplan\Access\Exceptions;

/**
 * Class CacheDataEmptyException
 *
 * @package Scaleplan\Access\Exceptions
 */
class CacheTypeNotSupportingException extends AccessException
{
    public const MESSAGE = 'Cache type not supporting.';
}

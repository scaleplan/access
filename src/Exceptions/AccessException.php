<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

/**
 * Class AccessException
 *
 * @package Scaleplan\Access\Exceptions
 */
class AccessException extends \Exception
{
    public const MESSAGE = 'Access error.';
    public const CODE = 403;

    /**
     * AccessException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message ?: static::MESSAGE, $code ?: static::CODE, $previous);
    }
}

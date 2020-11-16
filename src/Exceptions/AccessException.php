<?php
declare(strict_types=1);

namespace Scaleplan\Access\Exceptions;

use function Scaleplan\Translator\translate;

/**
 * Class AccessException
 *
 * @package Scaleplan\Access\Exceptions
 */
class AccessException extends \Exception
{
    public const MESSAGE = 'access.access-error';
    public const CODE = 403;

    /**
     * AccessException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     *
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        $message = $message ?: translate(static::MESSAGE) ?: static::MESSAGE;
        parent::__construct($message, $code ?: static::CODE, $previous);
    }
}

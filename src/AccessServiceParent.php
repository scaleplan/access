<?php

namespace Scaleplan\Access;

use phpDocumentor\Reflection\DocBlock;
use Scaleplan\Access\Exceptions\AccessDeniedException;
use Scaleplan\Access\Exceptions\AccessException;
use Scaleplan\Access\Exceptions\SupportingException;
use Scaleplan\Access\Exceptions\ValidationException;
use function Scaleplan\Translator\translate;

/**
 * Родитель для моделей - для проверки аргументов
 *
 * Class AccessServiceParent
 *
 * @package Scaleplan\Access
 */
class AccessServiceParent
{
    /**
     * @var Access
     */
    protected $access;

    /**
     * AccessControllerParent constructor.
     *
     * @param Access $access
     */
    public function __construct(Access $access)
    {
        $this->access = $access;
    }

    /**
     * @param array $args
     * @param \Reflector $reflector
     *
     * @return array
     *
     * @throws SupportingException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    protected static function formatArgs(array &$args, \Reflector $reflector) : array
    {
        if (!($reflector instanceof \ReflectionMethod) && !($reflector instanceof \ReflectionProperty)) {
            throw new SupportingException(translate('access.allows-reflections-only'));
        }

        $args = $args ? reset($args) : $args;
        if (!\is_array($args)) {
            throw new ValidationException(
                translate('access.method-accept-array', [':method' => $reflector->getName()])
            );
        }

        return $args;
    }

    /**
     * @param \ReflectionMethod $method
     * @param array $args
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AccessException
     * @throws SupportingException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function checkMethod(\ReflectionMethod $method, array &$args) : array
    {
        $docBlock = new DocBlock($method);
        if (empty($docBlock->getTagsByName($this->access->getConfig()->get(AccessConfig::ANNOTATION_LABEL_NAME)))) {
            throw new AccessDeniedException(translate('access.method-not-allowed', [':method' => $method->getName()]));
        }

        static::formatArgs($args, $method);
        $args = (new AccessSanitize($this->access, $method, $args))->sanitizeArgs();

        return $args;
    }

    /**
     * @param \ReflectionProperty $property
     * @param array $args
     *
     * @return array
     *
     * @throws AccessException
     * @throws SupportingException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function checkProperty(\ReflectionProperty $property, array &$args) : array
    {
        $docBlock = new DocBlock($property);
        if (empty($docBlock->getTagsByName($this->access->getConfig()->get(AccessConfig::ANNOTATION_LABEL_NAME)))) {
            throw new AccessException(translate('access.property-not-allowed', [':property' => $property->getName()]));
        }

        static::formatArgs($args, $property);
        $args = (new AccessSanitize($this->access, $property, $args))->sanitizeArgs();

        return $args;
    }

    /**
     * Аудит метода или свойства, и выполнение для методов
     *
     * @param string $methodName - имя метода
     * @param array $args - аргументы
     *
     * @return AccessServiceResult
     *
     * @throws AccessDeniedException
     * @throws AccessException
     * @throws SupportingException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    protected function checkServiceMethodEssence(string $methodName, array $args) : AccessServiceResult
    {
        $refClass = new \ReflectionClass(static::class);

        if ($refClass->hasMethod($methodName)) {
            $method = $refClass->getMethod($methodName);

            [$args, $isPlainArgs] = $this->checkMethod($method, $args);

            $method->setAccessible(true);

            return new AccessServiceResult(
                $refClass,
                $method,
                null,
                $args,
                $isPlainArgs
            );
        }

        if ($refClass->hasProperty($methodName)) {
            $property = $refClass->getProperty($methodName);

            [$args, $isPlainArgs] = $this->checkProperty($property, $args);

            $property->setAccessible(true);

            return new AccessServiceResult(
                $refClass,
                null,
                $property,
                $args,
                $isPlainArgs
            );
        }

        throw new AccessException(translate('access.method-does-not-exist', [':method' => $methodName]));
    }

    /**
     * Проверка переданных аргументов для метода или SQL-свойства
     *
     * @param string $methodName - имя метода или SQL-свойства
     * @param array $args - массив аргументов
     *
     * @return AccessServiceResult
     *
     * @throws AccessDeniedException
     * @throws AccessException
     * @throws SupportingException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    public function __call(string $methodName, array $args) : AccessServiceResult
    {
        return $this->checkServiceMethodEssence($methodName, $args);
    }
}

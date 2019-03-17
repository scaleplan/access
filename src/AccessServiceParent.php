<?php

namespace Scaleplan\Access;

use phpDocumentor\Reflection\DocBlock;
use Scaleplan\Access\Exceptions\AccessDeniedException;
use Scaleplan\Access\Exceptions\AccessException;
use Scaleplan\Access\Exceptions\SupportingException;
use Scaleplan\Access\Exceptions\ValidationException;

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
     * @param array $args
     * @param \Reflector $reflector
     *
     * @return array
     *
     * @throws SupportingException
     * @throws ValidationException
     */
    protected static function formatArgs(array &$args, \Reflector $reflector) : array
    {
        if (!($reflector instanceof \ReflectionMethod) && !($reflector instanceof \ReflectionProperty)) {
            throw new SupportingException();
        }

        $args = $args ? reset($args) : $args;
        if (!\is_array($args)) {
            throw new ValidationException("Метод {$reflector->getName()} принимает параметры в виде массива");
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
     */
    public static function checkMethod(\ReflectionMethod $method, array &$args) : array
    {
        /** @var Access $access */
        $access = Access::create();

        $docBlock = new DocBlock($method);
        if (empty($docBlock->getTagsByName($access->getConfig()->get(AccessConfig::ANNOTATION_LABEL_NAME)))) {
            throw new AccessDeniedException("Метод {$method->getName()} не доступен");
        }

        static::formatArgs($args, $method);
        $args = (new AccessSanitize($method, $args))->sanitizeArgs();

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
     */
    public static function checkProperty(\ReflectionProperty $property, array &$args) : array
    {
        /** @var Access $access */
        $access = Access::create();

        $docBlock = new DocBlock($property);
        if (empty($docBlock->getTagsByName($access->getConfig()->get(AccessConfig::ANNOTATION_LABEL_NAME)))) {
            throw new AccessException("Свойство {$property->getName()} не доступно");
        }

        static::formatArgs($args, $property);
        $args = (new AccessSanitize($property, $args))->sanitizeArgs();

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
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    protected static function checkServiceMethodEssence(string $methodName, array $args) : AccessServiceResult
    {
        $refClass = new \ReflectionClass(static::class);

        if ($refClass->hasMethod($methodName)) {
            $method = $refClass->getMethod($methodName);

            [$args, $isPlainArgs] = static::checkMethod($method, $args);

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

            [$args, $isPlainArgs] = static::checkProperty($property, $args);

            $property->setAccessible(true);

            return new AccessServiceResult(
                $refClass,
                null,
                $property,
                $args,
                $isPlainArgs
            );
        }

        throw new AccessException("Метод $methodName не существует");
    }

    /**
     * Проверка переданных аргументов для метода или SQL-свойства в статическом контексте
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
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    public static function __callStatic(string $methodName, array $args) : AccessServiceResult
    {
        return static::checkServiceMethodEssence($methodName, $args);
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
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    public function __call(string $methodName, array $args) : AccessServiceResult
    {
        return static::checkServiceMethodEssence($methodName, $args);
    }
}

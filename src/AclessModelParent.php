<?php

namespace Scaleplan\Access;

use phpDocumentor\Reflection\DocBlock;
use Scaleplan\Access\Constants\ConfigConstants;
use Scaleplan\Access\Exceptions\AccessDeniedException;
use Scaleplan\Access\Exceptions\AccessException;
use Scaleplan\Access\Exceptions\ValidationException;

/**
 * Родитель для моделей - для проверки аргументов
 *
 * Class AccessServiceParent
 *
 * @package avtomon
 */
class AccessServiceParent
{
    /**
     * @param array $args
     * @param \ReflectionMethod|\ReflectionProperty $method
     *
     * @return array
     *
     * @throws ValidationException
     */
    protected static function formatArgs(array &$args, \Reflector &$method): array
    {
            $args = $args ? reset($args) : $args;
            if (!\is_array($args)) {
                throw new ValidationException("Метод {$method->getName()} принимает параметры в виде массива");
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
     * @throws ValidationException
     */
    protected static function checkMethod(\ReflectionMethod &$method, array &$args) : array
    {
        /** @var Access $access */
        $access = Access::create();

        if (empty($docBlock = new DocBlock($method))
            ||
            empty($docBlock->getTagsByName($access->getConfig()[ConfigConstants::ANNOTATION_LABEL_NAME]))
        ) {
            throw new AccessDeniedException("Метод {$method->getName()} не доступен");
        }

        $isPlainArgs = empty(
            $docBlock->getTagsByName($access->getConfig(ConfigConstants::ARRAY_ARG_LABEL_NAME))
        );
        if ($isPlainArgs) {
            self::formatArgs($args, $method);
            $args = (new AccessSanitize($method, $args))->sanitizeArgs();
        }

        return [$args, $isPlainArgs];
    }

    /**
     * @param \ReflectionProperty $property
     * @param array $args
     *
     * @return array
     *
     * @throws AccessException
     * @throws ValidationException
     */
    protected static function checkProperty(\ReflectionProperty &$property, array &$args) : array
    {
        /** @var Access $access */
        $access = Access::create();

        if (empty($docBlock = new DocBlock($property)) || empty($docBlock->getTagsByName($access->getConfig()[ConfigConstants::ANNOTATION_LABEL_NAME]))) {
            throw new AccessException("Свойство {$property->getName()} не доступно");
        }

        self::formatArgs($args, $property);
        $args = (new AccessSanitize($property, $args))->sanitizeArgs();

        return [$args, false];
    }

    /**
     * Аудит метода или свойства, и выполнение для методов
     *
     * @param string $methodName - имя метода
     * @param array $args - аргументы
     *
     * @return AccessServiceResult
     *
     * @throws AccessException
     * @throws \ReflectionException
     */
    protected static function checkServiceMethodEssence(string $methodName, array $args) : AccessServiceResult
    {
        $refClass = new \ReflectionClass(static::class);

        if ($refClass->hasMethod($methodName)) {
            $method = $refClass->getMethod($methodName);

            [$args, $isPlainArgs] = self::checkMethod($method, $args);

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

            [$args, $isPlainArgs] = self::checkProperty($method, $args);

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
     * @throws AccessException
     * @throws \ReflectionException
     */
    public static function __callStatic(string $methodName, array $args) : AccessServiceResult
    {
        return self::checkServiceMethodEssence($methodName, $args);
    }

    /**
     * Проверка переданных аргументов для метода или SQL-свойства
     *
     * @param string $methodName - имя метода или SQL-свойства
     * @param array $args - массив аргументов
     *
     * @return AccessServiceResult
     *
     * @throws AccessException
     * @throws \ReflectionException
     */
    public function __call(string $methodName, array $args) : AccessServiceResult
    {
        return self::checkServiceMethodEssence($methodName, $args);
    }
}
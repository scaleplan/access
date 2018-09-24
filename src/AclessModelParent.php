<?php

namespace avtomon;

use phpDocumentor\Reflection\DocBlock;

/**
 * Родитель для моделей - для проверки аргументов
 *
 * Class AccessServiceParent
 * @package avtomon
 */
class AccessServiceParent
{
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
    protected static function checkServiceMethodEssence(string $methodName, array $args): AccessServiceResult
    {
        $formatArgs = function (array &$args) use (&$methodName): array {
            $args = $args ? reset($args) : $args;
            if (!\is_array($args)) {
                throw new AccessException("Метод $methodName принимает параметры в виде массива");
            }

            return $args;
        };

        $refclass = new \ReflectionClass(static::class);
        $access = Access::create();

        if ($refclass->hasMethod($methodName)) {
            $method = $refclass->getMethod($methodName);

            if (empty($docBlock = new DocBlock($method)) || empty($docBlock->getTagsByName($access->getConfig()['access_label']))) {
                throw new AccessException("Метод $methodName не доступен");
            }

            $isPlainArgs = empty($docBlock->getTagsByName($access->getConfig('access_array_arg')));
            if ($isPlainArgs) {
                $formatArgs($args);
                $args = (new AccessSanitize($method, $args))->sanitizeArgs();
            }

            $method->setAccessible(true);

            return new AccessServiceResult(
                $refclass,
                $method,
                null,
                $args,
                $isPlainArgs
            );
        }

        if ($refclass->hasProperty($methodName)) {
            $property = $refclass->getProperty($methodName);

            if (empty($docBlock = new DocBlock($property)) || empty($docBlock->getTagsByName($access->getConfig()['access_label']))) {
                throw new AccessException("Свойство $methodName не доступно");
            }

            $formatArgs($args);
            $args = (new AccessSanitize($property, $args))->sanitizeArgs();

            return new AccessServiceResult(
                $refclass,
                null,
                $property,
                $args,
                $isPlainArgs = false
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
    public static function __callStatic(string $methodName, array $args): AccessServiceResult
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
    public function __call(string $methodName, array $args): AccessServiceResult
    {
        return self::checkServiceMethodEssence($methodName, $args);
    }
}
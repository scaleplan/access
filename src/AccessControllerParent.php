<?php

namespace Scaleplan\Access;

use phpDocumentor\Reflection\DocBlock;
use Scaleplan\Access\Constants\ConfigConstants;
use Scaleplan\Access\Exceptions\AccessDeniedException;
use Scaleplan\Access\Exceptions\MethodNotFoundException;
use Scaleplan\Access\Exceptions\ValidationException;
use Scaleplan\Result\AbstractResult;
use Scaleplan\Result\DbResult;
use Scaleplan\Result\HTMLResult;

/**
 * Родитель для контроллеров - проверка прав доступа, фильтрация параметров
 *
 * Class AccessControllerParent
 *
 * @package Scaleplan\Access
 */
abstract class AccessControllerParent
{
    /**
     * Функции для выполнения перед исполнением метода контроллера
     *
     * @var array
     */
    protected static $before = [];

    /**
     * Результат выполения before-функции по умолчанию
     *
     * @var mixed
     */
    public static $beforeDefaultResult;

    /**
     * Функции для выполнения после исполнения метода контроллера
     *
     * @var array
     */
    protected static $after = [];

    /**
     * Результат выполения after-функции по умолчанию
     *
     * @var mixed
     */
    public static $afterDefaultResult;

    /**
     * Добавить функцию в конец массива функций выполняемых перед исполнением метода контроллера
     *
     * @param callable $function - функция
     */
    public static function pushBefore(callable $function): void
    {
        static::$before[] = $function;
    }

    /**
     * Добавить функцию в начало массива функций выполняемых перед исполнением метода контроллера
     *
     * @param callable $function - функция
     */
    public static function unshiftBefore(callable $function): void
    {
        array_unshift(static::$before, $function);
    }

    /**
     * Добавить функцию в заданную позицию массива функций выполняемых перед исполнением метода контроллера
     *
     * @param int $index - позиция вставки
     * @param callable $function - функция
     */
    public static function insertBefore(int $index, callable $function): void
    {
        array_merge(\array_slice(static::$before, 0, $index), $function, \array_slice(static::$before, $index));
    }

    /**
     * Добавить функцию в конец массива функций выполняемых после исполнения метода контроллера
     *
     * @param callable $function - функция
     */
    public static function pushAfter(callable $function): void
    {
        static::$after[] = $function;
    }

    /**
     * Добавить функцию в начало массива функций выполняемых после исполнения метода контроллера
     *
     * @param callable $function - функция
     */
    public static function unshiftAfter(callable $function): void
    {
        array_unshift(static::$after, $function);
    }

    /**
     * Добавить функцию в заданную позицию массива функций выполняемых после исполнения метода контроллера
     *
     * @param int $index - позиция вставки
     * @param callable $function - функция
     */
    public static function insertAfter(int $index, callable $function): void
    {
        array_merge(\array_slice(static::$after, 0, $index), $function, \array_slice(static::$after, $index));
    }

    /**
     * Удалить функцию или все функции, которые должны выполняться перед исполненим метода контроллера
     *
     * @param int $index - позиция удаления
     */
    public static function removeBefore(int $index): void
    {
        unset(static::$before[$index]);
    }

    /**
     * Удалить функцию или все функции, которые должны выполняться после исполнения метода контроллера
     *
     * @param int $index - позиция удаления
     */
    public static function removeAfter(int $index): void
    {
        unset(static::$after[$index]);
    }

    /**
     * Проверка прав доступа и входных данных для метода
     *
     * @param string $methodName - имя метода
     * @param array $args - аргументы выполнения
     * @param \object|null $obj - объект, к контекте которого должен выполниться метод (если нестатический)
     *
     * @return AbstractResult
     *
     * @throws AccessDeniedException
     * @throws Exceptions\AccessException
     * @throws Exceptions\AuthException
     * @throws Exceptions\ConfigException
     * @throws Exceptions\FormatException
     * @throws MethodNotFoundException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\Redis\Exceptions\RedisSingletonException
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    protected static function checkControllerMethod(string $methodName, array $args, object $obj = null): AbstractResult
    {
        $args = reset($args);
        if (!\is_array($args)) {
            throw new ValidationException("Метод $methodName принимает параметры в виде массива");
        }

        $refClass = new \ReflectionClass(static::class);

        if (!$refClass->hasMethod($methodName)) {
            throw new MethodNotFoundException("Метод $methodName не существует");
        }

        $method = $refClass->getMethod($methodName);
        /** @var Access $access */
        $access = Access::create();
        if (empty($docBlock = new DocBlock($method))
            ||
            empty($docBlock->getTagsByName($access->getConfig(ConfigConstants::ANNOTATION_LABEL_NAME)))
        ) {
            throw new AccessDeniedException("Метод $methodName не доступен");
        }

        $isPlainArgs = empty($docBlock->getTagsByName($access->getConfig(ConfigConstants::ARRAY_ARG_LABEL_NAME)));
        if ($isPlainArgs) {
            $isPlainArgs = false;
        } else {
            $params = $method->getParameters();
            if (!empty($params[0]) && $params[0]->isVariadic()) {
                $isPlainArgs = false;
            }
        }

        if (empty($docBlock->getTagsByName($access->getConfig(ConfigConstants::NO_CHECK_LABEL_NAME)))) {
            $access->checkMethodRights($method, $args, $refClass);
        }

        $args = $isPlainArgs ? (new AccessSanitize($method, $args))->sanitizeArgs() : $args;

        static::executeBeforeHandlers($method, $args);

        $method->setAccessible(true);
        $result = $isPlainArgs ? $method->invokeArgs($obj, $args) : $method->invoke($obj, $args);

        if ($result instanceof AbstractResult) {
            return $result;
        }

        if (\is_array($result)) {
            return new DbResult($result);
        }

        return new HTMLResult($result);
    }

    /**
     * Выполнить обработчики начала выполнения запроса
     *
     * @param null|\ReflectionMethod $method - отражение метода, который будет выполняться
     * @param array $args - его аргументы
     *
     * @return mixed
     */
    public static function executeBeforeHandlers(?\ReflectionMethod $method = null, array $args = [])
    {
        foreach (static::$before as $index => $func) {
            $result = $func($method, $args);
            if ($result === false) {
                break;
            }
        }

        return $result ?? static::$beforeDefaultResult;
    }

    /**
     * Выполнить обработчики окончания выполнения запроса
     *
     * @param \ReflectionMethod|null $method - отражение выполнявшегося метода констроллера
     * @param array $args - его аргументы
     * @param null $result - результат выполнения
     *
     * @return mixed
     */
    public static function executeAfterHandlers(\ReflectionMethod $method = null, array $args = [], $result = null)
    {
        foreach (static::$after as $index => $func) {
            $result = $func($method, $args, $result);
            if ($result === false) {
                break;
            }
        }

        return $result ?? static::$afterDefaultResult;
    }

    /**
     * Проверка прав доступа и входных данных для статических методов
     *
     * @param string $methodName - имя метода или SQL-свойства
     * @param array $args - массив аргументов
     *
     * @return AbstractResult
     *
     * @throws AccessDeniedException
     * @throws Exceptions\AccessException
     * @throws Exceptions\AuthException
     * @throws Exceptions\ConfigException
     * @throws Exceptions\FormatException
     * @throws MethodNotFoundException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\Redis\Exceptions\RedisSingletonException
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    public static function __callStatic(string $methodName, array $args): AbstractResult
    {
        return static::checkControllerMethod($methodName, $args);
    }

    /**
     * Проверка прав доступа и входных данных для нестатических методов
     *
     * @param string $methodName - имя метода или SQL-свойства
     * @param array $args - массив аргументов
     *
     * @return AbstractResult
     *
     * @throws AccessDeniedException
     * @throws Exceptions\AccessException
     * @throws Exceptions\AuthException
     * @throws Exceptions\ConfigException
     * @throws Exceptions\FormatException
     * @throws MethodNotFoundException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\Redis\Exceptions\RedisSingletonException
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    public function __call(string $methodName, array $args): AbstractResult
    {
        return static::checkControllerMethod($methodName, $args, $this);
    }
}
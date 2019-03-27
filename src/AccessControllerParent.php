<?php

namespace Scaleplan\Access;

use phpDocumentor\Reflection\DocBlock;
use Scaleplan\Access\Exceptions\AccessDeniedException;
use Scaleplan\Access\Exceptions\ClassNotFoundException;
use Scaleplan\Access\Exceptions\MethodNotFoundException;
use Scaleplan\Access\Exceptions\ValidationException;
use Scaleplan\Access\Hooks\MethodAllowed;
use Scaleplan\Access\Hooks\MethodExecuted;
use Scaleplan\Access\Hooks\SanitizePassed;
use function Scaleplan\Event\dispatch;
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
class AccessControllerParent
{
    /**
     * Проверка прав доступа и входных данных для метода
     *
     * @param string $className - имя класса
     * @param string $methodName - имя метода
     * @param array $args - аргументы выполнения
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws Exceptions\AccessException
     * @throws Exceptions\AuthException
     * @throws Exceptions\FormatException
     * @throws MethodNotFoundException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\Event\Exceptions\ClassNotImplementsEventInterfaceException
     */
    public static function checkControllerMethod(string $className, string $methodName, array $args): array
    {
        if (!\is_array($args)) {
            throw new ValidationException("Метод $methodName принимает параметры в виде массива");
        }

        if (!class_exists($className)) {
            throw new ClassNotFoundException("Метод $methodName не существует");
        }

        $refClass = new \ReflectionClass($className);

        if (!$refClass->hasMethod($methodName)) {
            throw new MethodNotFoundException("Метод $methodName не существует");
        }

        $refMethod = $refClass->getMethod($methodName);
        /** @var Access $access */
        $access = Access::getInstance();
        $docBlock = new DocBlock($refMethod);
        if (!$docBlock->getTagsByName($access->getConfig()->get(AccessConfig::ANNOTATION_LABEL_NAME))) {
            throw new AccessDeniedException("Метод $methodName не доступен");
        }

        if (empty($docBlock->getTagsByName($access->getConfig()->get(AccessConfig::NO_CHECK_LABEL_NAME)))) {
            $access->checkMethodRights($refMethod, $args, $refClass);
        }
        dispatch(MethodAllowed::class);

        $args = (new AccessSanitize($refMethod, $args))->sanitizeArgs();
        dispatch(SanitizePassed::class);

        return [$refClass, $refMethod, $args,];
    }

    /**
     * @param \ReflectionMethod $method
     * @param array $args
     * @param object|null $obj
     *
     * @return mixed|DbResult|HTMLResult
     *
     * @throws \Scaleplan\Event\Exceptions\ClassNotImplementsEventInterfaceException
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    protected static function execute(\ReflectionMethod $method, array &$args, object $obj = null)
    {
        $method->setAccessible(true);
        $result = $method->invokeArgs($obj, $args);
        dispatch(MethodExecuted::class);

        if ($result instanceof AbstractResult) {
            return $result;
        }

        if (\is_array($result)) {
            return new DbResult($result);
        }

        return new HTMLResult($result);
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
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\Event\Exceptions\ClassNotImplementsEventInterfaceException
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    public static function __callStatic(string $methodName, array $args): AbstractResult
    {
        $args = reset($args);
        [$refClass, $method, $args] = static::checkControllerMethod(static::class, $methodName, $args);
        return static::execute($method, $args);
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
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\Event\Exceptions\ClassNotImplementsEventInterfaceException
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    public function __call(string $methodName, array $args): AbstractResult
    {
        $args = reset($args);
        [$refClass, $method, $args] = static::checkControllerMethod(static::class, $methodName, $args);
        return static::execute($method, $args, $this);
    }
}

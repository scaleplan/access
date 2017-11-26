<?php

namespace avtomon;

/**
 * Родитель для контроллеров - проверка прав доступа
 *
 * Class AclessControllerParent
 * @package avtomon
 */
abstract class AclessControllerParent
{
    /**
     * Проверка прав доступа для статических методов
     *
     * @param string $methodName - имя метода или SQL-свойства
     * @param array $args - массив аргументов
     *
     * @return mixed
     *
     * @throws AclessException
     */
    protected static function __callStatic(string $methodName, array $args)
    {
        $refclass = new ReflectionClass(static::class);

        if (!($method = $refclass->getMethod($methodName)) || $method->isPrivate()) {
            throw new AclessException('Этот урл не доступен');
        }

        Acless::create()->checkMethodRights($method);
        return $method->invokeArgs(null, $args);
    }

    /**
     * Проверка прав доступа
     *
     * @param string $methodName - имя метода или SQL-свойства
     * @param array $args - массив аргументов
     *
     * @return mixed
     *
     * @throws AclessException
     */
    protected function __call(string $methodName, array $args)
    {
        $refclass = new ReflectionClass(static::class);

        if (!($method = $refclass->getMethod($methodName)) || $method->isPrivate()) {
            throw new AclessException('Этот урл не доступен');
        }

        Acless::create()->checkMethodRights($method);
        return $method->invokeArgs($this, $args);
    }
}
<?php

namespace avtomon;

/**
 * Родитель для моделей - для проверки аргументов
 *
 * Class AclessModelParent
 * @package avtomon
 */
abstract class AclessModelParent
{
    /**
     * Проверка переданных аргументов для метода или SQL-свойства в статическом контексте
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
        $args = reset($args);
        if (!is_array($args)) {
            throw new AclessException("Метод $name принимает параметры в виде массива");
        }

        $refclass = new ReflectionClass(static::class);

        if ($method = $refclass->getMethod($methodName)) {
            return $method->invokeArgs(null, AclessHelper::sanitizeMethodArgs($method, $args));
        } elseif (strripos($name, 'SQL') !== strlen($name) - 1 && $property = $refclass->getProperty($name)) {
            return $property->invoke(null, AclessHelper::sanitizeSQLPropertyArgs($property, $args));
        }
    }

    /**
     * Проверка переданных аргументов для метода или SQL-свойства
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
        $args = reset($args);
        if (!is_array($args)) {
            throw new AclessException("Метод $name принимает параметры в виде массива");
        }

        $refclass = new ReflectionClass(static::class);

        if ($method = $refclass->getMethod($methodName)) {
            return $method->invokeArgs($this, AclessHelper::sanitizeMethodArgs($method, $args));
        } elseif (strripos($name, 'SQL') !== strlen($name) - 1 && $property = $refclass->getProperty($name)) {
            return $property->invoke($this, AclessHelper::sanitizeSQLPropertyArgs($property, $args));
        }
    }
}
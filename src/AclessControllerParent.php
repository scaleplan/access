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
    private static function checkControllerMethod(string $methodName, array $args, object $obj = null)
    {
        $args = reset($args);
        if (!is_array($args)) {
            throw new AclessException("Метод $name принимает параметры в виде массива");
        }

        $refclass = new \ReflectionClass(static::class);

        if (!$refclass->hasMethod($methodName)) {
            throw new AclessException('Метод не существует');
        }

        $method = $refclass->getMethod($methodName);
        $acless = Acless::create();
        if (empty($doc = $method->getDocComment()) || empty($docBlock = $acless->docBlockFactory->create($doc)) || empty($docBlock->getTagsByName($acless->getConfig()['accless_label'])))
        {
            throw new AclessException('Метод не доступен');
        }

        $acless->checkMethodRights($method);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, AclessHelper::sanitizeMethodArgs($method, $args));
    }

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
        return self::checkControllerMethod($methodName, $args);
    }

    /**
     * Проверка прав доступа
     *
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
        return self::checkControllerMethod($methodName, $args, $this);
    }
}
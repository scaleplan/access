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
    protected static $before = [];
    protected static $beforeArgs = [];
    public static $beforeResult;

    protected static $after = [];
    protected static $afterArgs = [];
    public static $afterResult;

    /**
     * Добавить функцию в конец массива функций выполняемых перед исполнением метода контроллера
     *
     * @param \Closure $function - функция
     * @param array ...$args - агрументы функции
     *
     * @return int
     *
     * @throws AclessException
     */
    public static function pushBefore(\Closure $function, ...$args): int
    {
        $argCount = array_push(static::$beforeArgs, $args);
        $funcCount = array_push(static::$before, $function);
        if ($argCount !== $funcCount) {
            throw new AclessException('Количество элементов в массив before-функций не соответствует количеству массивов аргуметов');
        }

        return $funcCount;
    }

    /**
     * Добавить функцию в начало массива функций выполняемых перед исполнением метода контроллера
     *
     * @param \Closure $function - функция
     * @param array ...$args - аругументы функции
     *
     * @return int
     *
     * @throws AclessException
     */
    public static function unshiftBefore(\Closure $function, ...$args): int
    {
        $argCount = array_unshift(static::$beforeArgs, $args);
        $funcCount = array_unshift(static::$before, $function);
        if ($argCount !== $funcCount) {
            throw new AclessException('Количество элементов в массив before-функций не соответствует количеству массивов аргуметов');
        }

        return $funcCount;
    }

    /**
     * Добавить функцию в заданную позицию массива функций выполняемых перед исполнением метода контроллера
     *
     * @param int $index - позиция вставки
     * @param \Closure $function - функция
     * @param array ...$args - аргументы функции
     *
     * @return int
     *
     * @throws AclessException
     */
    public static function insertBefore(int $index, \Closure $function, ...$args): int
    {
        $argCount = count(array_merge(array_slice(static::$beforeArgs, 0, $index), $args, array_slice(static::$beforeArgs, $index)));
        $funcCount =  count(array_merge(array_slice(static::$before, 0, $index), $function, array_slice(static::$before, $index)));
        if ($argCount !== $funcCount) {
            throw new AclessException('Количество элементов в массив before-функций не соответствует количеству массивов аргуметов');
        }

        return $funcCount;
    }

    /**
     * Добавить функцию в конец массива функций выполняемых после исполнения метода контроллера
     *
     * @param \Closure $function - функция
     * @param array ...$args - аргументы функции
     *
     * @return int
     *
     * @throws AclessException
     */
    public static function pushAfter(\Closure $function, ...$args): int
    {
        $argCount = array_push(static::$afterArgs, $args);
        $funcCount = array_push(static::$after, $function);
        if ($argCount !== $funcCount) {
            throw new AclessException('Количество элементов в массив after-функций не соответствует количеству массивов аргуметов');
        }

        return $funcCount;
    }

    /**
     * Добавить функцию в начало массива функций выполняемых после исполнения метода контроллера
     *
     * @param \Closure $function - функция
     * @param array ...$args - аргументы функции
     *
     * @return int
     *
     * @throws AclessException
     */
    public static function unshiftAfter(\Closure $function, ...$args): int
    {
        $argCount = array_unshift(static::$afterArgs, $args);
        $funcCount = array_unshift(static::$after, $function);
        if ($argCount !== $funcCount) {
            throw new AclessException('Количество элементов в массив after-функций не соответствует количеству массивов аргуметов');
        }

        return $funcCount;
    }

    /**
     * Добавить функцию в заданную позицию массива функций выполняемых после исполнения метода контроллера
     *
     * @param int $index - позиция вставки
     * @param \Closure $function - функция
     * @param array ...$args - агрументы функции
     *
     * @return int
     *
     * @throws AclessException
     */
    public static function insertAfter(int $index, \Closure $function, ...$args): int
    {
        $argCount = count(array_merge(array_slice(static::$afterArgs, 0, $index), $args, array_slice(static::$afterArgs, $index)));
        $funcCount =  count(array_merge(array_slice(static::$after, 0, $index), $function, array_slice(static::$after, $index)));
        if ($argCount !== $funcCount) {
            throw new AclessException('Количество элементов в массив after-функций не соответствует количеству массивов аргуметов');
        }

        return $funcCount;
    }

    /**
     * Удалить функцию или все функции, которые должны выполняться перед исполненим метода контроллера
     *
     * @param int|null $index - позиция удаления
     *
     * @return int
     */
    public static function removeBefore(int $index = null): int
    {
        if ($index === null) {
            static::$beforeArgs = static::$before = [];
        } else {
            unset(static::$beforeArgs[$index], static::$before[$index]);
        }

        return count(static::$before);
    }

    /**
     * Удалить функцию или все функции, которые должны выполняться после исполнения метода контроллера
     *
     * @param int|null $index - позиция удаления
     *
     * @return int
     */
    public static function removeAfter(int $index = null): int
    {
        if ($index === null) {
            static::$afterArgs = static::$after = [];
        } else {
            unset(static::$afterArgs[$index], static::$after[$index]);
        }

        return count(static::$after);
    }



    private static function checkControllerMethod(string $methodName, array $args, object $obj = null)
    {
        $args = reset($args);
        if (!is_array($args)) {
            throw new AclessException("Метод $methodName принимает параметры в виде массива");
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
        $args = AclessHelper::sanitizeMethodArgs($method, $args);

        foreach (static::$before as $index => $func) {
            $result = $func(static::class, $args, $obj);
            if ($result === false) {
                break;
            }

            if ($result === null) {
                return static::$beforeResult;
            }
        }

        $method->setAccessible(true);
        $result = $method->invokeArgs($obj, $args);

        foreach (static::$after as $index => $func) {
            $result = $func($result, ...static::$afterArgs[$index]);
            if ($result === false) {
                break;
            }

            if ($result === null) {
                return static::$afterResult;
            }
        }

        return $result;
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
    public static function __callStatic(string $methodName, array $args)
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
    public function __call(string $methodName, array $args)
    {
        return self::checkControllerMethod($methodName, $args, $this);
    }
}
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
     * Функции для выполнения перед исполнением метода контроллера
     *
     * @var array
     */
    protected static $before = [];

    /**
     * Аргументы функций для выполнения перед исполнением метода контроллера
     *
     * @var array
     */
    protected static $beforeArgs = [];

    /**
     * Результат выполения before-функции по умолчанию
     *
     * @var
     */
    public static $beforeDefaultResult;

    /**
     * Функции для выполнения после исполнения метода контроллера
     *
     * @var array
     */
    protected static $after = [];

    /**
     * Аргументы функций для выполнения после исполнения метода контроллера
     *
     * @var array
     */
    protected static $afterArgs = [];

    /**
     * Результат выполения after-функции по умолчанию
     *
     * @var
     */
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
            throw new AclessException('Количество элементов в массив before-функций не соответствует количеству массивов аргуметов', 12);
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
            throw new AclessException('Количество элементов в массив before-функций не соответствует количеству массивов аргуметов', 13);
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
            throw new AclessException('Количество элементов в массив before-функций не соответствует количеству массивов аргуметов', 14);
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
            throw new AclessException('Количество элементов в массив after-функций не соответствует количеству массивов аргуметов', 15);
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
            throw new AclessException('Количество элементов в массив after-функций не соответствует количеству массивов аргуметов', 16);
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
            throw new AclessException('Количество элементов в массив after-функций не соответствует количеству массивов аргуметов', 17);
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

    /**
     * Проверка прав доступа и входных данных для метода
     *
     * @param string $methodName - имя метода
     * @param array $args - аргументы выполнения
     * @param object|null $obj - объект, к контекте которого должен выполниться метод (если нестатический)
     *
     * @return mixed
     *
     * @throws AclessException
     */
    protected static function checkControllerMethod(string $methodName, array $args, object $obj = null)
    {
        $args = reset($args);
        if (!is_array($args)) {
            throw new AclessException("Метод $methodName принимает параметры в виде массива", 18);
        }

        $refclass = new \ReflectionClass(static::class);

        if (!$refclass->hasMethod($methodName)) {
            throw new AclessException('Метод не существует', 19);
        }

        $method = $refclass->getMethod($methodName);
        $acless = Acless::create();
        if (empty($doc = $method->getDocComment()) || empty($docBlock = $acless->docBlockFactory->create($doc)) || empty($docBlock->getTagsByName($acless->getConfig('acless_label')))) {
            throw new AclessException('Метод не доступен', 20);
        }

        $isPlainArgs = empty($docBlock->getTagsByName($acless->getConfig('acless_array_arg')));
        if ($isPlainArgs && !empty($params[0]) && $params[0]->isVariadic()) {
            $isPlainArgs = false;
        }

        if (empty($docBlock->getTagsByName($acless->getConfig('acless_no_rights_check')))) {
            $acless->checkMethodRights($method, $args);
        }

        $args = $isPlainArgs ? AclessHelper::sanitizeMethodArgs($method, $args) : $args;

        foreach (static::$before as $index => $func) {
            $result = $func(static::class, $args, $obj);
            if ($result === false) {
                break;
            }

            if ($result === null) {
                return static::$beforeDefaultResult;
            }
        }

        $method->setAccessible(true);
        $result = $isPlainArgs ? $method->invokeArgs($obj, $args) : $method->invoke($obj, $args);

        return $result;
    }

    /**
     * Выполнить обработчики окончания выполнения запроса
     *
     * @return mixed
     */
    public static function executeAfterHandlers()
    {
        foreach (static::$after as $index => $func) {
            $result = $func($result, ...static::$afterArgs[$index]);
            if ($result === false) {
                break;
            }

            if ($result === null) {
                return static::$afterResult;
            }
        }
    }

    /**
     * Проверка прав доступа и входных данных для статических методов
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
     * Проверка прав доступа и входных данных для нестатических методов
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
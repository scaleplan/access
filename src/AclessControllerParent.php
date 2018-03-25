<?php

namespace avtomon;

/**
 * Родитель для контроллеров - проверка прав доступа, фильтрация параметров
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
     * Результат выполения before-функции по умолчанию
     *
     * @var
     */
    public static $beforeDefaultResult = null;

    /**
     * Функции для выполнения после исполнения метода контроллера
     *
     * @var array
     */
    protected static $after = [];

    /**
     * Результат выполения after-функции по умолчанию
     *
     * @var
     */
    public static $afterDefaultResult = null;

    /**
     * Добавить функцию в конец массива функций выполняемых перед исполнением метода контроллера
     *
     * @param callable $function - функция
     */
    public static function pushBefore(callable $function): void
    {
        array_push(static::$before, $function);
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
        array_merge(array_slice(static::$before, 0, $index), $function, array_slice(static::$before, $index));
    }

    /**
     * Добавить функцию в конец массива функций выполняемых после исполнения метода контроллера
     *
     * @param callable $function - функция
     */
    public static function pushAfter(\Closure $function, ...$args): void
    {
        array_push(static::$after, $function);
    }

    /**
     * Добавить функцию в начало массива функций выполняемых после исполнения метода контроллера
     *
     * @param callable $function - функция
     */
    public static function unshiftAfter(callable $function): int
    {
        array_unshift(static::$after, $function);
    }

    /**
     * Добавить функцию в заданную позицию массива функций выполняемых после исполнения метода контроллера
     *
     * @param int $index - позиция вставки
     * @param callable $function - функция
     */
    public static function insertAfter(int $index, callable $function)
    {
        array_merge(array_slice(static::$after, 0, $index), $function, array_slice(static::$after, $index));
    }

    /**
     * Удалить функцию или все функции, которые должны выполняться перед исполненим метода контроллера
     *
     * @param int|null $index - позиция удаления
     */
    public static function removeBefore(int $index = null): void
    {
        if ($index === null) {
            static::$before = [];
            return;
        }

        unset(static::$before[$index]);
    }

    /**
     * Удалить функцию или все функции, которые должны выполняться после исполнения метода контроллера
     *
     * @param int|null $index - позиция удаления
     */
    public static function removeAfter(int $index = null): void
    {
        if ($index === null) {
            static::$after = [];
        }

        unset(static::$after[$index]);
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
            $acless->checkMethodRights($method, $args, $refclass);
        }

        $args = $isPlainArgs ? AclessHelper::sanitizeMethodArgs($method, $args) : $args;

        self::executeBeforeHandlers($method, $args);

        $method->setAccessible(true);
        $result = $isPlainArgs ? $method->invokeArgs($obj, $args) : $method->invoke($obj, $args);

        return $result;
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

            if ($result === null) {
                return static::$beforeDefaultResult;
            }
        }
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

            if ($result === null) {
                return static::$afterDefaultResult;
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
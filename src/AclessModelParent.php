<?php

namespace avtomon;

/**
 * Класс результата выполнения модели
 *
 * Class AclessModelResult
 * @package avtomon
 */
class AclessModelResult
{
    /**
     * Отражение класса модели
     *
     * @var null|\ReflectionClass
     */
    private $class = null;

    /**
     * Отражение метода модели
     *
     * @var null|\ReflectionMethod
     */
    private $method = null;

    /**
     * Отражение свойства модели
     *
     * @var null|\ReflectionProperty
     */
    private $property = null;

    /**
     * Аргументы выполнения
     *
     * @var array|null
     */
    private $args = null;

    /**
     * true - метод модели принимает аргументы в виде набора
     * false - в виде ассоциативного массива
     *
     * @var bool
     */
    private $isPlainArgs = true;

    /**
     * Результат выполнения
     *
     * @var null
     */
    private $result = null;

    /**
     * AclessModelResult constructor
     *
     * @param \ReflectionClass $class
     * @param \ReflectionMethod|null $method
     * @param \ReflectionProperty|null $property
     * @param array|null $args
     * @param null $result
     */
    public function __construct(
        \ReflectionClass $class,
        \ReflectionMethod $method = null,
        \ReflectionProperty $property = null,
        array $args = null,
        bool $isPlainArgs = true,
        $result = null
    )
    {
        $this->class = $class;
        $this->method = $method;
        $this->property = $property;
        $this->args = $args;
        $this->isPlainArgs = $isPlainArgs;
        $this->result = $result;
    }

    /**
     * Геттер для отражения класса модели
     *
     * @return null|\ReflectionClass
     */
    public function getClass(): ?\ReflectionClass
    {
        return $this->class;
    }

    /**
     * Геттер для объекта класса модели
     *
     * @return null|object
     */
    public function getObject(): ?object
    {
        return $this->object;
    }

    /**
     * Геттер для отражения метода модели
     *
     * @return null|\ReflectionMethod
     */
    public function getMethod(): ?\ReflectionMethod
    {
        return $this->method;
    }

    /**
     * Геттер для отражения свойства модели
     *
     * @return null|\ReflectionProperty
     */
    public function getProperty(): ?\ReflectionProperty
    {
        return $this->property;
    }

    /**
     * Геттер для аргументов выполнения
     *
     * @return array|null
     */
    public function getArgs(): ?array
    {
        return $this->args;
    }

    public function getIsPlainArgs(): bool
    {
        return $this->isPlainArgs;
    }

    /**
     * Геттер для результата выполнения
     *
     * @return null|mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Вернуть результат выполнения, если результат - массив, иначе вернуть пустой массив
     *
     * @return array
     */
    public function getArrayResult(): array
    {
        return is_array($this->result) ? $this->result : [];
    }

    /**
     * Установить результат выполнения
     *
     * @param $result - результат
     * @param string $prefix - если результат - выборка из БД, то добавляем в начало ключей этот префикс
     *
     * @return mixed
     */
    public function setResult($result, string $prefix = '')
    {
        if ($prefix && is_array($result) && !empty($result[0])) {
            foreach ($result as &$record) {
                foreach ($record as $key => &$value) {
                    $record["$prefix_$key"] = $value;
                }
            }

            unset($record, $value);
        }

        return $this->result = $result;
    }

    /**
     * Вернуть объект в виде строки
     *
     * @return string
     */
    public function __toString(): string
    {
        return is_array($this->getResult()) ? json_encode($this->getResult(), JSON_UNESCAPED_UNICODE) : (string) $this->getResult();
    }

    /**
     * Вернуть первую запись результата, если результат - массив
     *
     * @return array
     */
    public function getFirstResult(): array
    {
        return !empty($this->result[0]) && is_array($this->result[0]) ? $this->result[0] : [];
    }

    /**
     * Вернуть поле id первой записи результата
     *
     * @return mixed|null
     */
    public function getResultId()
    {
        $firstResult = $this->getFirstResult();
        return !empty($firstResult['id']) ? $firstResult['id'] : null;
    }

    public function getResultFirstField()
    {
        $firstResult = $this->getFirstResult();
        if (!$firstResult) {
            throw new AclessException('Результирующий массив пуст');
        }

        return reset($firstResult);
    }
}

/**
 * Родитель для моделей - для проверки аргументов
 *
 * Class AclessModelParent
 * @package avtomon
 */
class AclessModelParent
{
    /**
     * Аудит метода или свойства, и выполнение для методов
     *
     * @param string $methodName - имя метода
     * @param array $args - аргументы
     *
     * @return AclessModelResult
     *
     * @throws AclessException
     */
    private static function checkModelMethodEssence(string $methodName, array $args)
    {
        $args = $args ? reset($args) : $args;
        if (!is_array($args)) {
            throw new AclessException("Метод $methodName принимает параметры в виде массива", 26);
        }

        $className = static::class;
        $refclass = new \ReflectionClass($className);
        $acless = Acless::create();

        if ($refclass->hasMethod($methodName)) {
            $method = $refclass->getMethod($methodName);

            if (empty($doc = $method->getDocComment()) || empty($docBlock = $acless->docBlockFactory->create($doc)) || empty($docBlock->getTagsByName($acless->getConfig()['acless_label']))) {
                throw new AclessException('Метод не доступен', 20);
            }

            $isPlainArgs = empty($docBlock->getTagsByName($acless->getConfig('acless_array_arg')));
            $args = $isPlainArgs ? AclessHelper::sanitizeMethodArgs($method, $args) : $args;

            $method->setAccessible(true);

            return new AclessModelResult(
                $refclass,
                $method,
                null,
                $args,
                $isPlainArgs
            );
        } elseif ($refclass->hasProperty($methodName)) {
            $property = $refclass->getProperty($methodName);

            if (!empty($doc = $property->getDocComment()) && !empty($docBlock = $acless->docBlockFactory->create($doc)) && !empty($docBlock->getTagsByName($acless->getConfig()['acless_label']))) {
                $args = AclessHelper::sanitizeSQLPropertyArgs($property, $args);
            }

            return new AclessModelResult(
                $refclass,
                null,
                $property,
                $args,
                $isPlainArgs = false
            );
        }

        throw new AclessException("Метод $methodName не существует", 33);
    }

    /**
     * Проверка переданных аргументов для метода или SQL-свойства в статическом контексте
     *
     * @param string $methodName - имя метода или SQL-свойства
     * @param array $args - массив аргументов
     *
     * @return AclessModelResult
     *
     * @throws AclessException
     */
    public static function __callStatic(string $methodName, array $args): ?AclessModelResult
    {
        return self::checkModelMethodEssence($methodName, $args);
    }

    /**
     * Проверка переданных аргументов для метода или SQL-свойства
     *
     * @param string $methodName - имя метода или SQL-свойства
     * @param array $args - массив аргументов
     *
     * @return AclessModelResult
     *
     * @throws AclessException
     */
    public function __call(string $methodName, array $args): ?AclessModelResult
    {
        return self::checkModelMethodEssence($methodName, $args);
    }
}
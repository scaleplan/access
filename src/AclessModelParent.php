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
     * Объект класса модели
     *
     * @var null|object
     */
    private $object = null;

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
     * Результат выполнения
     *
     * @var null
     */
    private $result = null;

    /**
     * AclessModelResult constructor
     *
     * @param \ReflectionClass $class
     * @param object|null $object
     * @param \ReflectionMethod|null $method
     * @param \ReflectionProperty|null $property
     * @param array|null $args
     * @param null $result
     */
    public function __construct(
        \ReflectionClass $class,
        object $object = null,
        \ReflectionMethod $method = null,
        \ReflectionProperty $property = null,
        array $args = null,
        $result = null)
    {
        $this->class = $class;
        $this->object = $object;
        $this->method = $method;
        $this->property = $property;
        $this->args = $args;
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
     * @param $result - рузультат
     *
     * @return mixed
     */
    public function setResult($result)
    {
        return $this->result = $result;
    }

    /**
     * Вернуть объект в виде строки
     *
     * @return string
     */
    public function __toString(): string
    {
        return is_array($this->getResult()) ? json_encode($this->getResult(), JSON_UNESCAPED_UNICODE) : $this->getResult();
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
     * @param object|null $obj - объект, в контексте которого должен выполняться метод
     *
     * @return AclessModelResult
     *
     * @throws AclessException
     */
    private static function checkModelMethodEssence(string $methodName, array $args, object $obj = null)
    {
        $args = $args ? reset($args) : $args;
        if (!is_array($args)) {
            throw new AclessException("Метод $methodName принимает параметры в виде массива", 26);
        }

        $className = static::class;
        $refclass = new \ReflectionClass($className);

        if ($refclass->hasMethod($methodName)) {
            $method = $refclass->getMethod($methodName);
            if ($method->isStatic() && $obj) {
                throw new AclessException('Метод должен вызываться в статическом контексте', 27);
            } elseif(!$method->isStatic() && !$obj) {
                throw new AclessException('Метод должен вызываться в контектсте объекта', 28);
            }

            $acless = Acless::create();
            if (!empty($doc = $method->getDocComment()) && !empty($docBlock = $acless->docBlockFactory->create($doc)) && !empty($docBlock->getTagsByName($acless->getConfig()['acless_label']))) {
                $args = AclessHelper::sanitizeMethodArgs($method, $args);
            }

            $method->setAccessible(true);

            return new AclessModelResult(
                $refclass,
                $obj,
                $method,
                null,
                $args,
                $method->invokeArgs($obj, $args)
            );
        } elseif ($refclass->hasProperty($methodName)) {
            $property = $refclass->getProperty($methodName);
            if ($property->isStatic() && $obj) {
                throw new AclessException('Метод должен вызываться в статическом контексте', 30);
            } elseif(!$property->isStatic() && !$obj) {
                throw new AclessException('Метод должен вызываться в контектсте объекта', 31);
            }

            $acless = Acless::create();
            if (!empty($doc = $property->getDocComment()) && !empty($docBlock = $acless->docBlockFactory->create($doc)) && !empty($docBlock->getTagsByName($acless->getConfig()['acless_label']))) {
                $args = AclessHelper::sanitizeSQLPropertyArgs($property, $args);
            }

            return new AclessModelResult(
                $refclass,
                $obj,
                null,
                $property,
                $args
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
        return self::checkModelMethodEssence($methodName, $args, $this);
    }
}
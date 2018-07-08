<?php

namespace avtomon;

/**
 * Класс результата выполнения модели
 *
 * Class AclessModelResult
 * @package avtomon
 */
class AclessModelResult extends DbResultItem
{
    /**
     * Отражение класса модели
     *
     * @var null|\ReflectionClass
     */
    protected $class;

    /**
     * Отражение метода модели
     *
     * @var null|\ReflectionMethod
     */
    protected $method;

    /**
     * Отражение свойства модели
     *
     * @var null|\ReflectionProperty
     */
    protected $property;

    /**
     * Аргументы выполнения
     *
     * @var array
     */
    protected $args = [];

    /**
     * true - метод модели принимает аргументы в виде набора
     * false - в виде ассоциативного массива
     *
     * @var bool
     */
    protected $isPlainArgs = true;

    /**
     * AclessModelResult constructor
     *
     * @param \ReflectionClass $class - отражение класса модели
     * @param \ReflectionMethod|null $method - отражение метода модели
     * @param \ReflectionProperty|null $property - отражение свойства модели
     * @param array|null $args - аргументы выполнения
     * @param bool $isPlainArgs - true - метод модели принимает аргументы в виде набора, false - в виде ассоциативного массива
     * @param null|mixed $result - результат
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
     * Будут ли параметры выполнения загружаться в виде последовательности аргументов
     *
     * @return bool
     */
    public function getIsPlainArgs(): bool
    {
        return $this->isPlainArgs;
    }

    /**
     * Добавить результат из другого объекта DbResultItem
     *
     * @param DbResultItem|null $rawResult
     */
    public function setRawResult(?DbResultItem $rawResult): void
    {
        $this->result = $rawResult ? $rawResult->result : null;
    }
}
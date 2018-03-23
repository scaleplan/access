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
    protected $class = null;

    /**
     * Отражение метода модели
     *
     * @var null|\ReflectionMethod
     */
    protected $method = null;

    /**
     * Отражение свойства модели
     *
     * @var null|\ReflectionProperty
     */
    protected $property = null;

    /**
     * Аргументы выполнения
     *
     * @var array|null
     */
    protected $args = null;

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

    /**
     * @return bool
     */
    public function getIsPlainArgs(): bool
    {
        return $this->isPlainArgs;
    }
}
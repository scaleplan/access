<?php

namespace avtomon;
use phpDocumentor\Reflection\DocBlock;

/**
 * Класс результата выполнения модели
 *
 * Class AclessServiceResult
 * @package avtomon
 */
class AclessServiceResult extends DbResultItem
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
     * AclessServiceResult constructor
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
        array $args = [],
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

    /**
     * Проверить тип возвращаемого значения по типам заданным в DOCBLOCK
     *
     * @throws AclessException
     */
    public function checkDocReturn(): void
    {
        $docBlock = new DocBlock($this->method ?? $this->property);
        $denyFuzzy = $docBlock->hasTag(Acless::create()->getConfig('deny_fuzzy'));
        $returnTypes = $docBlock->getTagsByName('return');
        $returnTypes = end($returnTypes);
        $returnTypes = array_map(function ($item) {
            return trim($item, '\\\ \0');
        }, explode('|', $returnTypes));

        if (!AclessSanitize::typeCheck($this->result, $returnTypes, $denyFuzzy)) {
            throw new AclessException("Тип возвращаемого значения не соответствует заданному типу $returnTypes");
        }
    }
}
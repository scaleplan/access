<?php
declare(strict_types=1);

namespace Scaleplan\Access;

use phpDocumentor\Reflection\DocBlock;
use Scaleplan\Access\Exceptions\ValidationException;
use Scaleplan\Result\DbResult;
use function Scaleplan\Translator\translate;

/**
 * Класс результата выполнения модели
 *
 * Class AccessServiceResult
 *
 * @package Scaleplan\Access
 */
class AccessServiceResult extends DbResult
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
     * @var bool
     */
    protected $denyFuzzy;

    /**
     * AccessServiceResult constructor.
     *
     * @param \ReflectionClass $class - отражение класса модели
     * @param \ReflectionMethod|null $method - отражение метода модели
     * @param \ReflectionProperty|null $property - отражение свойства модели
     * @param array|null $args - аргументы выполнения
     * @param bool $isPlainArgs - true - метод модели принимает аргументы в виде набора, false - в виде ассоциативного массива
     * @param null|mixed $result - результат
     * @param bool $denyFuzzy
     *
     * @throws \Scaleplan\Result\Exceptions\ResultException
     */
    public function __construct(
        \ReflectionClass $class,
        \ReflectionMethod $method = null,
        \ReflectionProperty $property = null,
        array $args = [],
        bool $isPlainArgs = true,
        $result = null,
        $denyFuzzy = true
    )
    {
        $this->class = $class;
        $this->method = $method;
        $this->property = $property;
        $this->args = $args;
        $this->isPlainArgs = $isPlainArgs;
        $this->denyFuzzy = $denyFuzzy;

        parent::__construct($result);
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
     * Добавить результат из другого объекта DbResult
     *
     * @param DbResult|null $rawResult
     */
    public function setRawResult(?DbResult $rawResult): void
    {
        $this->result = $rawResult ? $rawResult->result : null;
    }

    /**
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function checkDocReturn(): void
    {
        $docBlock = new DocBlock($this->method ?? $this->property);
        $returnTypes = $docBlock->getTagsByName('return');
        $returnTypes = end($returnTypes);
        $returnTypesArray = array_map(static function ($item) {
            return trim($item, '\\\ \0');
        }, explode('|', $returnTypes));

        if (!AccessSanitize::typeCheck($this->result, $returnTypesArray, $this->denyFuzzy)) {
            throw new ValidationException(translate('access.return-type-mismatch', ['type' => $returnTypes]));
        }
    }
}

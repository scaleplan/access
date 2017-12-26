<?php

namespace avtomon;

class AclessModelResult
{
    private $class = null;
    private $object = null;
    private $method = null;
    private $property = null;
    private $args = null;

    private $result = null;

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

    public function getClass(): ?\ReflectionClass
    {
        return $this->class;
    }

    public function getObject(): ?object
    {
        return $this->object;
    }

    public function getMethod(): ?\ReflectionMethod
    {
        return $this->method;
    }

    public function getProperty(): ?\ReflectionProperty
    {
        return $this->property;
    }

    public function getArgs(): ?array
    {
        return $this->args;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result): void
    {
        $this->result = $result;
    }

    public function __toString(): string
    {
        return json_encode($this->getResult(), JSON_UNESCAPED_UNICODE);
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
    private static function checkModelMethod(string $methodName, array $args, object $obj = null)
    {
        $args = reset($args);
        if (!is_array($args)) {
            throw new AclessException("Метод $methodName принимает параметры в виде массива");
        }

        $className = static::class;
        $refclass = new \ReflectionClass($className);

        if ($refclass->hasMethod($methodName)) {
            $method = $refclass->getMethod($methodName);
            if ($method->isStatic() && $obj) {
                throw new AclessException('Метод должен вызываться в статическом контексте');
            } elseif(!$method->isStatic() && !$obj) {
                throw new AclessException('Метод должен вызываться в контектсте объекта');
            }

            $acless = Acless::create();
            if (empty($doc = $method->getDocComment()) || empty($docBlock = $acless->docBlockFactory->create($doc)) || empty($docBlock->getTagsByName($acless->getConfig()['accless_label'])))
            {
                throw new AclessException('Метод не доступен');
            }

            $method->setAccessible(true);

            return new AclessModelResult(
                $refclass,
                $obj,
                $method,
                null,
                $args,
                $method->invokeArgs(null, AclessHelper::sanitizeMethodArgs($method, $args))
            );
        } elseif ($refclass->hasProperty($methodName)) {
            $property = $refclass->getProperty($methodName);
            if ($property->isStatic() && $obj) {
                throw new AclessException('Метод должен вызываться в статическом контексте');
            } elseif(!$property->isStatic() && !$obj) {
                throw new AclessException('Метод должен вызываться в контектсте объекта');
            }

            $acless = Acless::create();
            if (empty($doc = $property->getDocComment()) || empty($docBlock = $acless->docBlockFactory->create($doc)) || empty($docBlock->getTagsByName($acless->getConfig()['accless_label'])))
            {
                throw new AclessException('Свойство не доступно');
            }

            return new AclessModelResult(
                $refclass,
                $obj,
                null,
                $property,
                AclessHelper::sanitizeSQLPropertyArgs($property, $args)
            );
        }

        throw new AclessException('Метод не существует');
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
        return self::checkModelMethod($methodName, $args);
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
        return self::checkModelMethod($methodName, $args, $this);
    }
}
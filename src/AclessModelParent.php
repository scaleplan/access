<?php

namespace avtomon;
use phpDocumentor\Reflection\DocBlock;
use PhpParser\Comment\Doc;

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
     * @param \object|null $object - объект модели
     *
     * @return AclessModelResult
     *
     * @throws AclessException
     * @throws \ReflectionException
     */
    protected static function checkModelMethodEssence(string $methodName, array $args, object $object = null): AclessModelResult
    {
        $formatArgs = function (array &$args) use (&$methodName): array {
            $args = $args ? reset($args) : $args;
            if (!\is_array($args)) {
                throw new AclessException("Метод $methodName принимает параметры в виде массива", 26);
            }

            return $args;
        };

        $refclass = new \ReflectionClass(static::class);
        $acless = Acless::create();

        if ($refclass->hasMethod($methodName)) {
            $method = $refclass->getMethod($methodName);

            if (empty(empty($docBlock = new DocBlock($method)) || empty($docBlock->getTagsByName($acless->getConfig()['acless_label']))) {
                throw new AclessException("Метод $methodName не доступен", 20);
            }

            $isPlainArgs = empty($docBlock->getTagsByName($acless->getConfig('acless_array_arg')));
            if ($isPlainArgs) {
                $formatArgs($args);
                $args = AclessHelper::sanitizeMethodArgs($method, $args);
            }

            $method->setAccessible(true);

            return new AclessModelResult(
                $refclass,
                $method,
                null,
                $args,
                $isPlainArgs
            );
        }

        if ($refclass->hasProperty($methodName)) {
            $property = $refclass->getProperty($methodName);

            if (empty($docBlock = new DocBlock($property)) || empty($docBlock->getTagsByName($acless->getConfig()['acless_label']))) {
                throw new AclessException("Свойство $methodName не доступно", 20);
            }

            $formatArgs($args);
            $args = AclessHelper::sanitizeSQLPropertyArgs($property, $args, $object);

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
     * @throws \ReflectionException
     */
    public static function __callStatic(string $methodName, array $args): AclessModelResult
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
     * @throws \ReflectionException
     */
    public function __call(string $methodName, array $args): AclessModelResult
    {
        return self::checkModelMethodEssence($methodName, $args, $this);
    }
}
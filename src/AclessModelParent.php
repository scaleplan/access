<?php

namespace avtomon;


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
    protected static function checkModelMethodEssence(string $methodName, array $args)
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
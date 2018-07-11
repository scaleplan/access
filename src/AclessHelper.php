<?php

namespace avtomon;

use phpDocumentor\Reflection\DocBlockFactory;

/**
 * Класс хэлперов
 *
 * Class AclessHelper
 * @package avtomon
 */
class AclessHelper
{
    /**
     * Проверить аргументы метода
     *
     * @param \ReflectionMethod $method - Reflection-обертка для метода
     * @param array $args - массив аргументов
     *
     * @return array
     *
     * @throws AclessException
     */
    public static function sanitizeMethodArgs(\ReflectionMethod $method, array $args): array
    {
        $sanArgs = [];
        foreach ($method->getParameters() as &$param) {
            $paramType = $param->getType() ? $param->getType()->getName() : null;
            $paramName = $param->getName();

            if ($param->isVariadic()) {
                if (!$paramType) {
                    $sanArgs = array_merge($sanArgs, [$paramName => array_diff_key($args, $sanArgs)]);
                    break;
                }

                $sanArgs = array_merge($sanArgs, array_map(function ($item) use ($paramType, $paramName) {
                    if ($paramType && (string) $paramType !== \gettype($item)) {
                        $tmp = $item;
                        settype($tmp, (string) $paramType);
                        if ($tmp != $item) {
                            throw new AclessException("Неверный тип данных для параметра $paramName", 22);
                        }
                    }

                    return $item;
                }, $args));
                break;
            }

            if (!array_key_exists($paramName, $args) && !$param->isOptional()) {
                throw new AclessException("Отсутствует необходимый параметр $paramName", 21);
            }

            if ($param->isOptional() && (!array_key_exists($paramName, $args) || ($args[$paramName] == $param->getDefaultValue() && $param->getDefaultValue() === null))) {
                $sanArgs[$paramName] = $param->getDefaultValue();
                continue;
            }

            $arg = $args[$paramName];

            if ($paramType && (string) $paramType !== \gettype($arg)) {
                $tmp = $arg;
                settype($tmp, (string) $paramType);
                if ($tmp != $arg) {
                    throw new AclessException("Неверный тип данных для параметра $paramName", 23);
                }
            }

            \is_string($arg) && $arg = strip_tags($arg);

            $sanArgs[$paramName] = $arg;
        }

        return $sanArgs;
    }

    /**
     * Проверить аргументы для свойства-метода
     *
     * @param \ReflectionProperty $property - Reflection-обертка для SQL-свойства
     * @param array $args - массив аргументов
     * @param \object|null $object - объект модели
     *
     * @return array
     *
     * @throws AclessException
     */
    public static function sanitizeSQLPropertyArgs(\ReflectionProperty $property, array $args, object $object = null): array
    {
        $sanArgs = [];
        if (!$property->isPublic()) {
            $property->setAccessible(true);
        }

        $docBlock = DocBlockFactory::createInstance()->create($property->getDocComment());
        $allParams = $optionParams = [];
        $docParams = $docBlock->getTagsByName('param');
        if ($docParams) {
            foreach ($docParams as $docParam) {
                $allParams[$docParam->getVariableName()] = $docParam->getType();
                $paramDescription = (string) $docParam->getDescription();
                if ($paramDescription && stripos($paramDescription, '(optional)') !== false) {
                    $optionParams[$docParam->getVariableName()] = $docParam->getType();
                }
            }
        } else {
            $sqlParams = self::getSQLParams($property->getValue($object));
            $allParams = array_fill_keys($sqlParams[0], null);
            $optionParams = array_fill_keys($sqlParams[1], null);
        }

        foreach ($allParams as $paramName => $paramType) {
            if (!array_key_exists($paramName, $args)) {
                if (!array_key_exists($paramName, $optionParams)) {
                    throw new AclessException("Не хватает параметра $paramName");
                }

                continue;
            }

            $arg = $args[$paramName];
            if ($paramType && (string) $paramType !== \gettype($arg)) {
                $tmp = $arg;
                settype($tmp, (string) $paramType);
                if ($tmp != $arg) {
                    throw new AclessException("Тип параметра $paramName не соответствует заданному типу {$docParams[$paramName]['type']}", 25);
                }
            }

            \is_string($arg) && $arg = strip_tags($arg);

            $sanArgs[$paramName] = $arg;
        }

        return $sanArgs;
    }

    /**
     * Получить из SQL-запроса все параметры
     *
     * @param $sql
     *
     * @return array
     */
    public static function getSQLParams($sql): array
    {
        $all = $optional = [];
        if (preg_match_all('/[^:]+?:([\w_\-]+).*?/i', $sql, $matches)) {
            $all = array_unique($matches[1]);
        }

        if (preg_match_all('/\[[^\]]*?:([\w_\-]+)[^\[]*?\]/i', $sql, $matches)) {
            $optional = array_unique($matches[1]);
        }

        return [$all, $optional];
    }

    /**
     * Превратить строку в виде camelCase в строку вида dashed (camelCase -> camel-case)
     *
     * @param string $str - строка в camelCase
     *
     * @return string
     */
    public static function camel2dashed(string $str): string
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $str));
    }
}
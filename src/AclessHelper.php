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
            $paramType = $param->getType()->getName();
            $paramName = $param->getName();

            if (!isset($args[$paramName]) && !$param->isOptional()) {
                throw new AclessException("Отсутствует необходимый параметр $paramName");
            }

            if ($param->isOptional() && (!isset($args[$paramName]) || ($args[$paramName] == $param->getDefaultValue() && is_null($param->getDefaultValue())))) {
                $sanArgs[] = $param->getDefaultValue();
                continue;
            }

            if ($param->isVariadic()) {
                if (!$paramType) {
                    $sanArgs = array_merge($sanArgs, $args);
                    break;
                }

                $sanArgs = array_merge($sanArgs, array_map(function ($item) use ($param, $paramType) {
                    if ($paramType && (string) $paramType !== gettype($item)) {
                        $tmp = $item;
                        settype($tmp, (string) $paramType);
                        if ($tmp != $item) {
                            throw new AclessException("Неверный тип данных для параметра $paramName");
                        }
                    }

                    return $item;
                }, $args));
                break;
            }

            $arg = $args[$paramName];

            if ($paramType && (string) $paramType !== gettype($arg)) {
                $tmp = $arg;
                settype($tmp, (string) $paramType);
                if ($tmp != $arg) {
                    throw new AclessException("Неверный тип данных для параметра $paramName");
                }
            }

            gettype($arg) === 'string' && $arg = strip_tags($arg);

            $sanArgs[] = $arg;
        }

        return $sanArgs;
    }

    /**
     * Проверить аргументы для свойства-метода
     *
     * @param \ReflectionProperty $property - Reflection-обертка для SQL-свойства
     * @param array $args - массив аргументов
     *
     * @return array
     *
     * @throws AclessException
     */
    public static function sanitizeSQLPropertyArgs(\ReflectionProperty $property, array $args): array
    {
        $sanArgs = [];
        if (!$property->isPublic())
        {
            $property->setAccessible(true);
        }

        $params = self::getSQLParams($property->getValue());
        $property->setAccessible(false);
        $docBlock = DocBlockFactory::createInstance()->create($property->getDocComment());
        $docParams = [];
        foreach ($docBlock->getTagsByName('param') as $docParam) {
            $docParams[$docParam->getVariableName()] = [
                'type' => $docParam->getType()
            ];
        }

        foreach ($params as $paramName) {
            if (!in_array($paramName, array_keys($args))) {
                throw new AclessException("Не хватает параметра $paramName");
            }

            $arg = $args[$paramName];
            $paramType = $docParams[$paramName]['type'] ?? null;
            if ($paramType && (string) $paramType !== gettype($arg)) {
                $tmp = $arg;
                settype($tmp, (string) $paramType);
                if ($tmp != $arg) {
                    throw new AclessException("Тип параметра $paramName не соответствует заданному типу {$docParams[$paramName]['type']}");
                }
            }

            gettype($arg) === 'string' && $arg = strip_tags($arg);

            $sanArgs[$paramName] = $arg;
        }

        return $sanArgs;
    }

    /**
     * Получить из SQL-запроса все параметры
     *
     * @param $sql
     * @return array
     */
    public static function getSQLParams($sql): array
    {
        if (preg_match_all('/[^:]:([\w\d_\-]+)/i', $sql, $matches))
        {
            return array_unique($matches[1]);
        }

        return [];
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
<?php

namespace avtomon;

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
            $arg = $args[$paramName];

            if ($param->isVariadic()) {
                if (!$paramType) {
                    $sanArgs = array_merge($sanArgs, $args);
                    break;
                }

                $sanArgs = array_merge($sanArgs, array_map(function ($item) use ($param, $paramType) {
                    if ($paramType && $paramType !== gettype($item) && $tmp = $item && settype($tmp, $paramType) && $tmp != $item) {
                        throw new AclessException("Неверный тип данных для параметра $name");
                    }

                    return $item;
                }, $args));
                break;
            }

            if (!in_array($paramName, array_keys($args))) {
                if (!$item->isOptional()) {
                    throw new AclessException("Отсутствует необходимый параметр $name");
                }

                continue;
            }

            if ($paramType && $paramType !== gettype($arg) && $tmp = $arg && settype($tmp, $paramType) && $tmp != $arg) {
                throw new AclessException("Неверный тип данных для параметра $name");
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
        $params = self::getSQLParams($property->getValue());
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
            if ($paramType && $paramType !== gettype($arg) && $tmp = $arg && settype($tmp, $paramType) && $tmp != $arg) {
                throw new AclessException("Тип параметра $paramName не соответствует заданному типу {$docParams[$paramName]['type']}");
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
        if (preg_match_all('/:([\w\d_\-]+)/i', $sql, $matches))
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
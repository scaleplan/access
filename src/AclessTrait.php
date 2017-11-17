<?php

namespace avtomon;

use phpDocumentor\Reflection\DocBlockFactory;
use app\classes\Helper;

trait AclessModelTrait
{
    public static function __callStatic($methodName, $args)
    {


        $args = reset($args);
        if (!is_array($args)) {
            throw new AclessException("Метод $name принимает параметры в виде массива");
        }

        $refclass = new ReflectionClass(static::class);
        //$controller = $refclass->isInstantiable() ? $refclass->newInstance() : null;
        $sanArgs = [];
        if ($method = $refclass->getMethod($methodName)) {
            foreach ($method->getParameters() as &$param) {
                $paramType = $param->getType()->getName();
                $paramName = $param->getName();
                $arg = $args[$methodName];

                if ($param->isVariadic()) {
                    if (!$param->getType()) {
                        $sanArgs = array_merge($sanArgs, $args);
                        break;
                    }

                    $sanArgs = array_merge($sanArgs, array_map(function ($item) use ($param, $type) {
                        if ($type !== gettype($item)) {
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

                if ($type && $type !== gettype($args[$pos])) {
                    throw new AclessException("Неверный тип данных для параметра $name");
                }

                $sanArgs[] = $arg;
            }

            return $method->invokeArgs($methodName, $sanArgs);
        } elseif (strripos($name, 'SQL') !== strlen($name) - 1 && $property = $refclass->getProperty($name)) {
            $params = Helper::getSQLParams($property->getValue());
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

                if (!empty($docParams[$paramName]['type']) && $docParams[$paramName]['type'] !== gettype($args[$paramName])) {
                    throw new AclessException("Тип параметра $paramName не соответствует заданному типу {$docParams[$paramName]['type']}");
                }

                $sanArgs[$paramName] = $args[$paramName];
            }

            return $method->invoke($methodName, $sanArgs);
        }
    }

    public static function before(string &$name, array &$args)
    {

    }
}
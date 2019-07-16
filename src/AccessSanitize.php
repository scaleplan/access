<?php

namespace Scaleplan\Access;

use phpDocumentor\Reflection\DocBlock;
use Scaleplan\Access\Exceptions\AccessException;
use Scaleplan\Access\Exceptions\SupportingException;
use Scaleplan\Access\Exceptions\ValidationException;
use Scaleplan\DTO\DTO;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use Scaleplan\Helpers\NameConverter;
use function Scaleplan\Translator\translate;

/**
 * Класс проверки аргументов выполнения
 *
 * Class AccessSanitize
 *
 * @package Scaleplan\Access
 */
class AccessSanitize
{
    public const TYPE_VALIDATION_GROUP = 'type';

    /**
     * Отражение метода или SQL-свойства
     *
     * @var \ReflectionMethod|\ReflectionProperty
     */
    protected $reflector;

    /**
     * Массив аргументов
     *
     * @var array
     */
    protected $args = [];

    /**
     * Массив очещенных аргументов
     *
     * @var array|null
     */
    protected $sanitizedArgs;

    /**
     * Объект Access
     *
     * @var Access
     */
    protected $access;

    /**
     * AccessSanitize constructor.
     *
     * @param Access $access - объект Access
     * @param \Reflector $reflector - отражение метода или SQL-свойства
     * @param array $args - массив аргументов
     *
     * @throws SupportingException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function __construct(Access $access, \Reflector $reflector, array $args)
    {
        if (!($reflector instanceof \ReflectionMethod) && !($reflector instanceof \ReflectionProperty)) {
            throw new SupportingException(translate('access.allows-reflections-only'));
        }

        $this->access = $access;
        $this->reflector = $reflector;
        $this->args = $args;
    }

    /**
     * Проверить и очистить аргументы
     *
     * @return array
     *
     * @throws AccessException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function sanitizeArgs() : array
    {
        if ($this->sanitizedArgs !== null) {
            return $this->sanitizedArgs;
        }

        if ($this->reflector instanceof \ReflectionMethod) {
            return $this->sanitizedArgs = $this->sanitizeMethodArgs($this->reflector, $this->args);
        }

        return $this->sanitizedArgs = $this->sanitizeSQLPropertyArgs($this->reflector, $this->args);
    }

    /**
     * @param $args
     * @param string $typeName
     *
     * @return DTO|null
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     */
    public static function getDTO($args, string $typeName) : ?DTO
    {
        if (!is_subclass_of($typeName, DTO::class)) {
            return null;
        }

        /** @var DTO $param */
        $param = new $typeName($args);
        $param->validate([static::TYPE_VALIDATION_GROUP]);
        $param->validate();
        return $param;
    }

    /**
     * @param \ReflectionParameter $param
     * @param array $args
     *
     * @return mixed
     *
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    protected static function getParamValue(\ReflectionParameter $param, array $args)
    {
        $paramName = $param->getName();

        if (array_key_exists($paramName, $args)) {
            return $args[$paramName];
        }

        $snakeParamName = NameConverter::camelCaseToSnakeCase($paramName);
        if (array_key_exists($snakeParamName, $args)) {
            return $args[$snakeParamName];
        }

        if ($param->isOptional()) {
            return $param->getDefaultValue();
        }

        throw new ValidationException(translate('access.required-parameter-missing', [':parameter' => $paramName]));
    }

    /**
     * Проверить аргументы метода
     *
     * @param \ReflectionMethod $method - Reflection-обертка для метода
     * @param array $args - массив аргументов
     *
     * @return array
     *
     * @throws AccessException
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function sanitizeMethodArgs(\ReflectionMethod $method, array $args) : array
    {
        $sanArgs = [];
        $docBlock = new DocBlock($method);
        /** @var ParamTag[] $docParams */
        [$docParams] = static::getDocParams($docBlock->getTagsByName('param'));

        foreach ($method->getParameters() as &$param) {
            $paramName = $param->getName();
            $docParamType = $docParams[$paramName] ? $docParams[$paramName]->getType() : '';
            $paramType = $param->getType() ? $param->getType()->getName() : $docParamType;

            if ($param->isVariadic()) {
                if (!$paramType) {
                    $sanArgs = array_merge($sanArgs, [$paramName => array_diff_key($args, $sanArgs)]);
                    break;
                }

                $sanArgs = array_merge($sanArgs, array_map(static function ($arg) use ($paramType, $paramName, $docBlock) {
                    $this->docTypeCheck($arg, $paramName, $paramType, $docBlock);
                    return $arg;
                }, $args));
                break;
            }

            $dto = static::getDTO($args, $paramType);
            if ($dto) {
                $sanArgs[$paramName] = $dto;
                continue;
            }

            $arg = static::getParamValue($param, $args);
            if ($param->isOptional() && $arg === $param->getDefaultValue()) {
                $sanArgs[$paramName] = $arg;
                continue;
            }

            $this->docTypeCheck($arg, $paramName, $paramType, $docBlock);
            //\is_string($arg) && $arg = \strip_tags($arg);
            $sanArgs[$paramName] = $arg;
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
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function sanitizeSQLPropertyArgs(\ReflectionProperty $property, array $args) : array
    {
        $sanArgs = [];
        if (!$property->isPublic()) {
            $property->setAccessible(true);
        }

        $docBlock = new DocBlock($property);
        $docParams = $docBlock->getTagsByName('param');
        if ($docParams) {
            [$allParams, $optionParams] = static::getDocParams($docParams);
        } else {
            $value = $property->getDeclaringClass()->getDefaultProperties()[$property->getName()];
            $sqlParams = static::getSQLParams($value);
            $allParams = array_fill_keys($sqlParams[0], null);
            $optionParams = array_fill_keys($sqlParams[1], null);
        }

        foreach ($allParams as $paramName => $param) {
            static::argAvailabilityCheck($paramName, $args, $optionParams);

            if (!array_key_exists($paramName, $args)) {
                continue;
            }

            $arg = $args[$paramName];
            if ($param instanceof ParamTag && $param->getType()) {
                $this->docTypeCheck($arg, $paramName, (string)$param->getType(), $docBlock);
            }

            \is_string($arg) && $arg = strip_tags($arg);

            $sanArgs[$paramName] = $arg;
        }

        return $sanArgs;
    }

    /**
     * Проверка наличия аргументов
     *
     * @param string $paramName - имя параметра
     * @param array $args - массив аргументов
     * @param array $optionParams - массив опциональных параметров
     *
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    protected static function argAvailabilityCheck(string $paramName, array $args, array $optionParams) : void
    {
        if (!array_key_exists($paramName, $args) && !array_key_exists($paramName, $optionParams)) {
            throw new ValidationException(translate('access.required-parameter-missing', [':parameter' => $paramName]));
        }
    }

    /**
     * Вернуть массив DOCBLOCK-параметров и подгруппу необязательных параметров
     *
     * @param DocBlock\Tag\ParamTag[] $docParams - исходный массив параметров
     *
     * @return array
     */
    protected static function getDocParams(array $docParams) : array
    {
        $allParams = $optionParams = [];
        foreach ($docParams as $docParam) {
            $varName = ltrim($docParam->getVariableName(), '$');
            $allParams[$varName] = $docParam;
            $paramDescription = (string)$docParam->getDescription();
            if ($paramDescription && stripos($paramDescription, '(optional)') !== false) {
                $optionParams[$varName] = $docParam;
            }
        }

        return [$allParams, $optionParams];
    }

    /**
     * Проверка аргументов на соответствие типу
     *
     * @param $arg - значение аргумента
     * @param string $paramName - имя аргумента/параметра
     * @param string $paramType - требуемый тип или группа типов
     * @param DocBlock $docBlock - ссылка объект DOCBLOCK метода или свойства
     *
     * @throws ValidationException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    protected function docTypeCheck(&$arg, string $paramName, string $paramType, DocBlock $docBlock) : void
    {
        if (!$paramType) {
            return;
        }

        $denyFuzzy = $docBlock->hasTag($this->access->getConfig()->get(AccessConfig::DOCBLOCK_CHECK_LABEL_NAME));

        $paramTypes = array_map(static function ($item) {
            return trim($item, '\\\ \0');
        }, explode('|', $paramType));

        if (!static::typeCheck($arg, $paramTypes, $denyFuzzy)) {
            throw new ValidationException(
                translate('access.parameter-type-mismatch', [':parameter' => $paramName, ':type' => $paramType])
            );
        }
    }

    /**
     * Проверка значения на соответствие типу
     *
     * @param $value - значение
     * @param array $types - принимаемые типы
     * @param bool $denyFuzzy - строгое ли сравнение используется
     *
     * @return bool
     */
    public static function typeCheck(&$value, array $types, $denyFuzzy = true) : bool
    {
        if (!$types && \in_array('mixed', $types, true)) {
            return true;
        }

        $argType = \gettype($value);
        if ($argType === 'object' && !array_filter($types, static function ($type) use ($value, $denyFuzzy, $argType) {
                $result = $value instanceof $type;
                if (!$result && !$denyFuzzy) {
                    $result = preg_match("/^(\w+\\)*$type$/", $argType);
                }

                return $result;
            })) {
            return false;
        }

        if ($denyFuzzy) {
            return false;
        }

        foreach ($types as $type) {
            $tmpType = gettype($value);
            $tmp = $value;
            settype($tmp, $type);
            settype($tmp, $tmpType);
            if ($tmp == $value) {
                settype($value, $type);
                return true;
            }
        }

        return false;
    }

    /**
     * Получить из SQL-запроса все параметры
     *
     * @param $sql
     *
     * @return array
     */
    public static function getSQLParams($sql) : array
    {
        $all = $optional = [];
        if (preg_match_all('/[^:]+?:([\w_\-]+).*?/', $sql, $matches)) {
            $all = array_unique($matches[1]);
        }

        if (preg_match_all('/\[[^\]]*?:([\w_\-]+)[^\[]*?\]/', $sql, $matches)) {
            $optional = array_unique($matches[1]);
        }

        return [$all, $optional];
    }
}

<?php

namespace Scaleplan\Access;

use phpDocumentor\Reflection\DocBlock;
use Scaleplan\Access\Constants\DbConstants;
use Scaleplan\Access\Exceptions\AccessDeniedException;
use Scaleplan\Access\Exceptions\AuthException;
use Scaleplan\Access\Exceptions\FormatException;
use function Scaleplan\Translator\translate;

/**
 * Класс формирования списка урлов и проверки прав
 *
 * Class Access
 *
 * @package Scaleplan\Access
 */
class Access extends AccessAbstract
{
    /**
     * Инстанс класса
     *
     * @var null|Access
     */
    protected static $instance;

    /**
     * Вернуть информацию о всех доступных пользователю урлах или о каком-то конкретном урле
     *
     * @param string|null $url - текст урла
     *
     * @return array
     */
    public function getAccessRights(string $url = '') : array
    {
        if ($url) {
            return $this->cache->getAccessRight($url);
        }

        return $this->cache->getAllAccessRights();
    }

    /**
     * @param \ReflectionClass|null $refClass
     * @param \ReflectionMethod $refMethod
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthException
     * @throws FormatException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    protected function checkOnlyMethod(?\ReflectionClass $refClass, \ReflectionMethod $refMethod) : array
    {
        $className = $refClass ? $refClass->getName() : $refMethod->getDeclaringClass()->getName();
        $url = $this->methodToURL($className, $refMethod->getName());
        $accessRight = $this->getAccessRights($url);
        if (!$accessRight) {
            if ($this->getUserId() === $this->config->get(AccessConfig::GUEST_USER_ID_DIRECTIVE_NAME)) {
                throw new AuthException(translate('access.lets-auth'));
            }

            throw new AccessDeniedException(
                translate('access.method-not-allowed', [':method' => $refMethod->getName()])
            );
        }

        return $accessRight;
    }

    /**
     * @param array|null $methodDefaults
     * @param \ReflectionMethod $refMethod
     *
     * @return array|null
     *
     * @throws \ReflectionException
     */
    protected static function getMethodDefaults(?array &$methodDefaults, \ReflectionMethod $refMethod) : ?array
    {
        if ($methodDefaults === null) {
            $methodDefaults = [];
            foreach ($refMethod->getParameters() as $parameter) {
                if ($parameter->isOptional()) {
                    $methodDefaults[$parameter->getName()] = $parameter->getDefaultValue();
                }
            }
        }

        return $methodDefaults;
    }

    /**
     * @param array $accessRight
     * @param array $args
     * @param \ReflectionMethod $refMethod
     *
     * @throws AccessDeniedException
     * @throws FormatException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    protected function checkMethodFilters(array $accessRight, array $args, \ReflectionMethod $refMethod) : void
    {
        $docBlock = new DocBlock($refMethod);
        if (!$docBlock
            || empty($tag = $docBlock->getTagsByName($this->config->get(AccessConfig::FILTER_DIRECTIVE_NAME)))) {
            return;
        }

        $docParam = end($tag);
        $filterName = trim($docParam->getDescription()) ?: $this->config->get(AccessConfig::DEFAULT_FILTER_NAME);
        /*if ($filters) {
            $filters = array_map('trim', explode(',', $filters));

            $accessRight[DbConstants::IDS_FIELD_NAME] = array_map(function ($item) {
                return array_map('trim', explode($this->config->get(AccessConfig::FILTER_SEPARATOR_NAME), $item));
            }, json_decode($accessRight[DbConstants::IDS_FIELD_NAME], true));

            if (empty($args)) {
                throw new FormatException(translate('access.parameter-list-empty'));
            }

            $methodDefaults = null;
            static::getMethodDefaults($methodDefaults, $refMethod);

            if (\count($accessRight[DbConstants::IDS_FIELD_NAME][0]) !== \count($filters)) {
                throw new FormatException(translate('access.parameters-lists-mismatch'));
            }

            $checkValue = [];
            foreach ($filters as $filter) {
                if (!array_key_exists($filter, $args)
                    && array_key_exists($filter, $methodDefaults)) {
                    $args[$filter] = $methodDefaults[$filter];
                }

                $checkValue[] = $args[$filter];
            }

            if (array_intersect($filters, array_keys($args)) !== $filters) {
                throw new FormatException(translate('access.filters-mismatch'));
            }

            if (($accessRight[DbConstants::IS_ALLOW_FIELD_NAME]
                    && !\in_array($checkValue, $accessRight[DbConstants::IDS_FIELD_NAME], true))
                ||
                (!$accessRight[DbConstants::IS_ALLOW_FIELD_NAME]
                    && \in_array($checkValue, $accessRight[DbConstants::IDS_FIELD_NAME], true))
            ) {
                throw new AccessDeniedException(translate('access.id-not-allowed', [':filters' => $filters]));
            }
        }*/
        $filterValues = array_map(function ($item) {
            return array_map('trim', explode($this->config->get(AccessConfig::FILTER_SEPARATOR_NAME), $item));
        }, json_decode($accessRight[DbConstants::IDS_FIELD_NAME], true));

        if (!$filterValues) {
            return;
        }

        $isAllow = $accessRight[DbConstants::IS_ALLOW_FIELD_NAME];

        if (empty($args)) {
            throw new FormatException(translate('access.parameter-list-empty'));
        }

        if (!array_key_exists($filterName, $args)) {
            return;
            //throw new FormatException(translate('access.filters-mismatch'));
        }

        if ((!\in_array($args[$filterName], $filterValues) && $isAllow)
            || (\in_array($args[$filterName], $filterValues) && !$isAllow)) {
            throw new AccessDeniedException(translate('access.id-not-allowed', [':filter' => $filterName]));
        }
    }

    /**
     * Проверить доступ к методу
     *
     * @param \ReflectionMethod $refMethod - Reflection-обертка для метода
     * @param array $args - параметры выполнения
     * @param \ReflectionClass|null $refClass - класс метода
     *
     * @return bool
     *
     * @throws AccessDeniedException
     * @throws AuthException
     * @throws FormatException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function checkMethodRights(
        \ReflectionMethod $refMethod,
        array $args,
        \ReflectionClass $refClass = null
    ) : bool
    {
        $accessRight = $this->checkOnlyMethod($refClass, $refMethod);
        $this->checkMethodFilters($accessRight, $args, $refMethod);

        return true;
    }

    /**
     * Проверить доступ к файлу
     *
     * @param string $filePath - путь к файлу
     *
     * @return bool
     *
     * @throws AccessDeniedException
     * @throws AuthException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function checkFileRights(string $filePath) : bool
    {
        if (empty($accessRight = $this->getAccessRights($filePath))) {
            if ($this->getUserId() === $this->config->get(AccessConfig::GUEST_USER_ID_DIRECTIVE_NAME)) {
                throw new AuthException(translate('access.lets-auth'));
            }

            throw new AccessDeniedException(translate('access.file-not-allowed'));
        }

        return true;
    }

    /**
     * Сформировать урл из имени контроллера и имени метода
     *
     * @param string $className - имя класса
     * @param string $methodName - имя метода
     *
     * @return string
     *
     * @throws FormatException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function methodToURL(string $className, string $methodName) : string
    {
        foreach ($this->config->get(AccessConfig::CONTROLLERS_SECTION_NAME) as $controllerDir) {
            if (empty($controllerDir['path'])) {
                throw new FormatException(translate('access.path-missing'));
            }

            $className = strtr($className, [$controllerDir['namespace'] => '', $controllerDir['class_postfix'] => '']);
            $methodName = str_replace($controllerDir['method_prefix'], '', $methodName);
        }

        $methodName = str_replace(
            '\\',
            '/',
            trim($className, '\/ ') . '\\' . trim($methodName, '\/ ')
        );

        return AccessHelper::camel2dashed($methodName);
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public function getForbiddenSelectors(string $url) : array
    {
        return $this->cache->getForbiddenSelectors($url);
    }
}

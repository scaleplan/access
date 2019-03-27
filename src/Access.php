<?php

namespace Scaleplan\Access;

use phpDocumentor\Reflection\DocBlock;
use Scaleplan\Access\Constants\DbConstants;
use Scaleplan\Access\Exceptions\AccessDeniedException;
use Scaleplan\Access\Exceptions\AuthException;
use Scaleplan\Access\Exceptions\FormatException;

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
     * Разделитель значений фильтров
     *
     * @var string
     */
    protected $filterSeparator = ':';

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
     * @param \ReflectionMethod $refMethod
     *
     * @return DocBlock|null
     */
    protected function getMethodDocBlock(\ReflectionMethod $refMethod) : ?DocBlock
    {
        $docBlock = new DocBlock($refMethod);
        if (!$tag = $docBlock->getTagsByName($this->config->get(AccessConfig::ANNOTATION_LABEL_NAME))) {
            return null;
        }

        return $docBlock;
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
     */
    protected function checkOnlyMethod(?\ReflectionClass $refClass, \ReflectionMethod $refMethod) : array
    {
        $className = $refClass ? $refClass->getName() : $refMethod->getDeclaringClass()->getName();
        $url = $this->methodToURL($className, $refMethod->getName());
        $accessRight = $this->getAccessRights($url);
        if (!$accessRight) {
            if ($this->getUserId() === $this->config->get(AccessConfig::GUEST_USER_ID_DIRECTIVE_NAME)) {
                throw new AuthException('Авторизуйтесь на сайте');
            }

            throw new AccessDeniedException('Метод не разрешен Вам для выпонения');
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
     */
    protected function checkMethodFilters(array $accessRight, array $args, \ReflectionMethod $refMethod) : void
    {
        $docParam = end($tag);
        $filters = trim($docParam->getDescription());
        if ($filters) {
            $filters = array_map('trim', explode(',', $filters));

            $accessRight[DbConstants::VALUES_FIELD_NAME] = array_map(function ($item) {
                return array_map('trim', explode($this->filterSeparator, $item));
            }, json_decode($accessRight[DbConstants::VALUES_FIELD_NAME], true));

            if (empty($args)) {
                throw new FormatException('Список параметров выполнения действия пуст');
            }

            $methodDefaults = null;
            $getMethodDefaults = static::getMethodDefaults($methodDefaults, $refMethod);

            if (\count($accessRight[DbConstants::VALUES_FIELD_NAME][0]) !== \count($filters)) {
                throw new FormatException(
                    'Количество фильтрующих параметров не соответствует количеству фильтрующих значений'
                );
            }

            $checkValue = [];
            foreach ($filters as $filter) {
                if (!array_key_exists($filter, $args)
                    && array_key_exists($filter, $getMethodDefaults($methodDefaults))) {
                    $args[$filter] = $getMethodDefaults($methodDefaults)[$filter];
                }

                $checkValue[] = $args[$filter];
            }

            if (array_intersect($filters, array_keys($args)) !== $filters) {
                throw new FormatException(
                    'Список параметров выполнения действия не содержит все фильтрующие параметры'
                );
            }

            if (($accessRight[DbConstants::IS_ALLOW_FIELD_NAME]
                    && !\in_array($checkValue, $accessRight[DbConstants::VALUES_FIELD_NAME], true))
                ||
                (!$accessRight[DbConstants::IS_ALLOW_FIELD_NAME]
                    && \in_array($checkValue, $accessRight[DbConstants::VALUES_FIELD_NAME], true))
            ) {
                throw new AccessDeniedException(
                    "Выполнение метода с такими параметрами $filters Вам не разрешено"
                );
            }
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
     */
    public function checkMethodRights(
        \ReflectionMethod $refMethod,
        array $args,
        \ReflectionClass $refClass = null
    ) : bool
    {
        $docBlock = $this->getMethodDocBlock($refMethod);
        if (!$docBlock) {
            return true;
        }

        $accessRight = $this->checkOnlyMethod($refClass, $refMethod);

        if (empty($tag = $docBlock->getTagsByName($this->config->get(AccessConfig::FILTER_DIRECTIVE_NAME)))) {
            return true;
        }

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
     */
    public function checkFileRights(string $filePath) : bool
    {
        if (empty($accessRight = $this->getAccessRights($filePath))) {
            if ($this->getUserId() === $this->config->get(AccessConfig::GUEST_USER_ID_DIRECTIVE_NAME)) {
                throw new AuthException('Авторизуйтесь на сайте');
            }

            throw new AccessDeniedException('Файл Вам не доступен');
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
     */
    public function methodToURL(string $className, string $methodName) : string
    {
        foreach ($this->config->get(AccessConfig::CONTROLLERS_SECTION_NAME) as $controllerDir) {
            if (empty($controllerDir['path'])) {
                throw new FormatException(
                    'Неверный формат данных о директории с контроллерами: нет необходимого параметра "path"'
                );
            }

            $className = str_replace($controllerDir['namespace'], '', $className);
        }

        $methodName = str_replace(
            '\\',
            '/',
            trim($className, '\/ ') . '\\' . trim($methodName, '\/ ')
        );

        return AccessHelper::camel2dashed(preg_replace('(Controller|action)', '', $methodName));
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

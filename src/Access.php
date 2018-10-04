<?php

namespace Scaleplan\Access;

use phpDocumentor\Reflection\DocBlock;
use Scaleplan\Access\Constants\DbConstants;
use Scaleplan\Access\Constants\SessionConstants;
use Scaleplan\Access\Exceptions\AccessDeniedException;
use Scaleplan\Access\Constants\ConfigConstants;
use Scaleplan\Access\Exceptions\AccessException;
use Scaleplan\Access\Exceptions\AuthException;
use Scaleplan\Access\Exceptions\ConfigException;
use Scaleplan\Access\Exceptions\FormatException;
use Scaleplan\Redis\RedisSingleton;

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
     *
     * @throws ConfigException
     * @throws \Scaleplan\Redis\Exceptions\RedisSingletonException
     */
    public function getAccessRights(string $url = ''): array
    {
        $cacheStorageName = $this->config[ConfigConstants::CACHE_STORAGE_SECTION_NAME];
        switch ($cacheStorageName) {
            case 'redis':
                if (empty($this->config[$cacheStorageName]['socket'])) {
                    throw new ConfigException('В конфигурации не задан путь к Redis-сокету');
                }

                $this->cs = $this->cs ?? RedisSingleton::create($this->config[$cacheStorageName]['socket']);
                if ($url) {
                    return json_decode($this->cs->hGet("user_id:{$this->userId}", $url), true)  ?? [];
                }

                return array_map(function ($item) {
                    return json_decode($item, true) ?? $item;
                }, array_filter($this->cs->hGetAll("user_id:{$this->userId}")));

            case 'session':
                return $url
                    ? ($_SESSION[SessionConstants::SESSION_ACCESS_RIGHTS_SECTION_NAME][$url] ?? [])
                    : array_filter($_SESSION[SessionConstants::SESSION_ACCESS_RIGHTS_SECTION_NAME]);

            default:
                throw new ConfigException(
                    "Драйвер 
                    {$cacheStorageName} 
                    кэширующего хранилища не поддерживается системой"
                );
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
     * @throws AccessException
     * @throws AuthException
     * @throws ConfigException
     * @throws FormatException
     * @throws \Scaleplan\Redis\Exceptions\RedisSingletonException
     */
    public function checkMethodRights(
        \ReflectionMethod $refMethod,
        array $args,
        \ReflectionClass $refClass = null
    ): bool
    {
        if (empty($docBlock = new DocBlock($refMethod))
            || empty($tag = $docBlock->getTagsByName($this->config[ConfigConstants::ANNOTATION_LABEL_NAME]))
        ) {
            return true;
        }

        $className = $refClass ? $refClass->getName() : $refMethod->getDeclaringClass()->getName();
        $url = $this->methodToURL($className, $refMethod->getName());
        if (empty($accessRight = $this->getAccessRights($url))) {
            if ($this->getUserId() === $this->getConfig(ConfigConstants::GUEST_USER_ID_DIRECTIVE_NAME)) {
                throw new AuthException('Авторизуйтесь на сайте');
            }

            throw new AccessDeniedException('Метод не разрешен Вам для выпонения');
        }

        if (empty($tag = $docBlock->getTagsByName($this->config[ConfigConstants::FILTER_DIRECTIVE_NAME]))) {
            return true;
        }

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
            $getMethodDefaults = function (?array &$methodDefaults) use ($refMethod): ?array {
                if ($methodDefaults === null) {
                    $methodDefaults = [];
                    foreach ($refMethod->getParameters() as $parameter) {
                        if ($parameter->isOptional()) {
                            $methodDefaults[$parameter->getName()] = $parameter->getDefaultValue();
                        }
                    }
                }

                return $methodDefaults;
            };

            if (\count($accessRight[DbConstants::VALUES_FIELD_NAME][0]) !== \count($filters)) {
                throw new FormatException(
                    'Количество фильтрующих параметров не соответствует количеству фильтрующих значений'
                );
            }

            $checkValue = [];
            foreach ($filters as $filter) {
                if (!array_key_exists($filter, $args)
                    && array_key_exists($filter, $getMethodDefaults($methodDefaults))
                ) {
                    $args[$filter] = $getMethodDefaults($methodDefaults)[$filter];
                }

                $checkValue[] = $args[$filter];
            }

            if (array_intersect($filters, array_keys($args)) !== $filters) {
                throw new FormatException(
                    'Список параметров выполнения действия не содержит все фильтрующие параметры'
                );
            }

            if (
                (
                    $accessRight[DbConstants::IS_ALLOW_FIELD_NAME]
                    && !\in_array($checkValue, $accessRight[DbConstants::VALUES_FIELD_NAME], true)
                )
                ||
                (
                    !$accessRight[DbConstants::IS_ALLOW_FIELD_NAME]
                    && \in_array($checkValue, $accessRight[DbConstants::VALUES_FIELD_NAME], true)
                )
            ) {
                throw new AccessDeniedException(
                    "Выполнение метода с такими параметрами $filters Вам не разрешено"
                );
            }
        }

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
     * @throws ConfigException
     * @throws \Scaleplan\Redis\Exceptions\RedisSingletonException
     */
    public function checkFileRights(string $filePath): bool
    {
        if (empty($accessRight = $this->getAccessRights($filePath))) {
            if ($this->getUserId() === $this->getConfig(ConfigConstants::GUEST_USER_ID_DIRECTIVE_NAME)) {
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
    public function methodToURL(string $className, string $methodName): string
    {
        foreach ($this->config[ConfigConstants::CONTROLLERS_SECTION_NAME] as $controllerDir) {
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
     * Сгенерировать массив урлов контроллеров
     *
     * @param string $controllerFileName - имя файла контроллера
     * @param string|null $controllerNamespace - пространство имен для конроллера, если есть
     *
     * @return array
     *
     * @throws AccessException
     * @throws \ReflectionException
     */
    protected function generateControllerURLs(string $controllerFileName, string $controllerNamespace = null): array
    {
        $controller = trim(explode('.', $controllerFileName)[0]);
        $controllerNamespace = trim($controllerNamespace, '/\ ');
        if ($controllerNamespace) {
            $controllerNamespace = "\\$controllerNamespace\\";
            $controller = str_replace('/', '\\', $controller);
        } else {
            $controller = explode('/', $controller);
            $controller = end($controller);
        }

        $refClass = new \ReflectionClass("$controllerNamespace$controller");

        $urls = [];
        $sql = 'SELECT id, schema_name, table_name FROM access.model_type';
        $models = $this->getPSConnection()->query($sql)->fetchAll(\PDO::FETCH_COLUMN);

        $seachService = function (string $schema, string $table) use ($models): ?int {
            if (empty($models) || !\is_array($models) || empty($schema) || empty($table)) {
                return null;
            }

            foreach ($models as $model) {
                if ($model['schema_name'] !== $schema || $model['table_name'] !== $table) {
                    continue;
                }

                return $model['id'];
            }

            return null;
        };

        foreach ($refClass->getMethods() as $method) {
            if (empty($docBlock = new DocBlock($method))
                || empty($docBlock->getTagsByName($this->config['access_label']))
            ) {
                continue;
            }

            if ($method->getDeclaringClass()->getName() === 'avtomon\AccessControllerParent') {
                continue;
            }

            $methodName = str_replace('action', '', $method->getName());

            $accessSchema = $docBlock->getTagsByName($this->config[ConfigConstants::ANNOTATION_SCHEMA_LABEL_NAME]);
            $accessSchema = end($accessSchema);
            $accessTables = $docBlock->getTagsByName($this->config[ConfigConstants::ANNOTATION_TABLE_LABEL_NAME]);
            $accessTables = end($accessTables);
            $accessUrlType = $docBlock->getTagsByName($this->config[ConfigConstants::ANNOTATION_URL_TYPE_LABEL_NAME]);
            $accessUrlType = end($accessUrlType);

            $modelId = $seachService($accessSchema, $accessTables);

            $url = [
                'text' => '/' . strtolower(
                    str_replace(
                        'Controller', '', $controller)
                    ) . '/' . AccessHelper::camel2dashed($methodName),
                'name' => $docBlock->getText(),
                'model_type_id' => $modelId,
                'type' => $accessUrlType
            ];

            $urls[] = $url;
        }

        return $urls;
    }

    /**
     * Найти все файлы в каталоге, включая вложенные директории
     *
     * @param string $dir - путь к каталогу
     *
     * @return array
     */
    protected function getRecursivePaths(string $dir): array
    {
        $dir = rtrim($dir, '/\ ');
        $paths = scandir($dir, SCANDIR_SORT_NONE);
        unset($paths[0], $paths[1]);
        $result = [];

        foreach ($paths as &$path) {
            if (is_dir("$dir/$path")) {
                $result = array_merge($result, array_map(function ($item) use ($path, $dir) {
                    return "$dir/$path/$item";
                }, $this->getRecursivePaths("$dir/$path")));
            } else {
                $result[] = $path;
            }
        }

        unset($path);

        return $result;
    }

    /**
     * Возвращает все урлы контроллеров
     *
     * @return array
     *
     * @throws AccessException
     * @throws FormatException
     * @throws \ReflectionException
     */
    public function getControllerURLs(): array
    {
        if (empty($this->config[ConfigConstants::CONTROLLERS_SECTION_NAME])) {
            return null;
        }

        $urls = [];
        foreach ($this->config[ConfigConstants::CONTROLLERS_SECTION_NAME] as $controllerDir) {
            if (empty($controllerDir['path'])) {
                throw new FormatException(
                    'Неверный формат данных о директории с контроллерами: нет необходимого параметра "path"'
                );
            }

            $controllers = array_map(function ($item) use ($controllerDir) {
                return trim(str_replace($controllerDir['path'], '', $item), '\/ ');
            }, $this->getRecursivePaths($controllerDir['path']));

            foreach ($controllers as $controller) {
                $controllerNamespace = $controllerDir['namespace'] ?? null;
                $urls = array_merge($urls, $this->generateControllerURLs($controller, $controllerNamespace));
            }
        }

        return $urls;
    }

    /**
     * Возвращает все урлы файлов
     *
     * @return array
     */
    public function getFilesURLs(): array
    {
        if (empty($this->config[ConfigConstants::FILES_SECTION_NAME])) {
            return null;
        }

        $urls = [];
        foreach ($this->config[ConfigConstants::FILES_SECTION_NAME] as $fileDir) {
            $urls = array_merge($urls, array_map(function ($item) use ($fileDir) {
                return [
                    'text' => trim(str_replace($fileDir, '', $item), '\/ '),
                    'name' => null,
                    'model_type_id' => null,
                    'type' => null
                ];
            }, $this->getRecursivePaths($fileDir)));
        }

        return $urls;
    }

    /**
     * Возвращает урлы, непосредственно указанные в конфигурационном файле
     *
     * @return array
     */
    public function getPlainURLs(): array
    {
        return array_map(function ($item) {
            return [
                'text' => trim($item, '\/ '),
                'name' => null,
                'model_type_id' => null,
                'type' => null
            ];
        }, array_filter($this->config[ConfigConstants::URLS_SECTION_NAME]));
    }

    /**
     * Возращает все собранные урлы
     *
     * @return array
     *
     * @throws AccessException
     * @throws \ReflectionException
     */
    public function getAllURLs(): array
    {
        return array_merge($this->getControllerURLs(), $this->getFilesURLs(), $this->getPlainURLs());
    }
}

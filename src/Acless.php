<?php

namespace avtomon;

use phpDocumentor\Reflection\DocBlock;

/**
 * Класс исключений
 *
 * Class AclessException
 * @package avtomon
 */
class AclessException extends CustomException
{
}

/**
 * Класс формирования списка урлов и проверки прав
 *
 * Class Acless
 * @package avtomon
 */
class Acless extends AclessAbstract
{
    /**
     * Код ошибки Acless указывающий на закрытый доступ к ресурсу
     */
    public const ACLESS_403_ERROR_CODE = 43;

    /**
     * Код ошибки Acless указывающий на неразрешенный неавторизованный запрос
     */
    public const ACLESS_UNAUTH_ERROR_CODE = 47;

    /**
     * Инстанс класса
     *
     * @var null|Acless
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
     * @throws AclessException
     * @throws RedisSingletonException
     */
    public function getAccessRights(string $url = ''): array
    {
        switch ($this->config['cache_storage']) {
            case 'redis':
                if (empty($this->config['redis']['socket'])) {
                    throw new AclessException('В конфигурации не задан путь к Redis-сокету');
                }

                $this->cs = $this->cs ?? RedisSingleton::create($this->config['redis']['socket']);
                if ($url) {
                    return json_decode($this->cs->hGet("user_id:{$this->userId}", $url), true)  ?? [];
                }

                return array_map(function ($item) {
                    return json_decode($item, true) ?? $item;
                }, array_filter($this->cs->hGetAll("user_id:{$this->userId}")));

            case 'session':
                return $url ? ($_SESSION['access_rights'][$url] ?? []) : array_filter($_SESSION['access_rights']);

            default:
                throw new AclessException("Драйвер {$this->config['cache_storage']} кэширующего хранилища не поддерживается системой");
        }
    }

    /**
     * Проверить доступ к методу
     *
     * @param \Reflector $refMethod - Reflection-обертка для метода
     * @param array $args - параметры выполнения
     * @param \ReflectionClass|null $refClass - класс метода
     *
     * @return bool
     *
     * @throws AclessException
     * @throws RedisSingletonException
     */
    public function checkMethodRights(\Reflector $refMethod, array $args, \ReflectionClass $refClass = null): bool
    {
        if (empty($docBlock = new DocBlock($refMethod)) || empty($tag = $docBlock->getTagsByName($this->config['acless_label']))) {
            return true;
        }

        $className = $refClass ? $refClass->getName() : $refMethod->getDeclaringClass()->getName();
        $url = $this->methodToURL($className, $refMethod->getName());
        if (empty($accessRight = $this->getAccessRights($url))) {
            if ($this->getUserId() === $this->getConfig('guest_user_id')) {
                throw new AclessException('Авторизуйтесь на сайте', self::ACLESS_UNAUTH_ERROR_CODE);
            }

            throw new AclessException('Метод не разрешен Вам для выпонения', self::ACLESS_403_ERROR_CODE);
        }

        if (empty($tag = $docBlock->getTagsByName($this->config['acless_filter_label']))) {
            return true;
        }

        $docParam = end($tag);
        $filters = trim($docParam->getDescription() ? $docParam->getDescription()->render() : '');
        if ($filters) {
            $filters = array_map('trim', explode(',', $filters));

            $accessRight['values'] = array_map(function ($item) {
                return array_map('trim', explode($this->filterSeparator, $item));
            }, json_decode($accessRight['values'], true));

            if (empty($args)) {
                throw new AclessException('Список параметров выполнения действия пуст');
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

            if (\count($accessRight['values'][0]) !== \count($filters)) {
                throw new AclessException('Количество фильтрующих параметров не соответствует количеству фильтрующих значений');
            }

            $checkValue = [];
            foreach ($filters as $filter) {
                if (!array_key_exists($filter, $args) && array_key_exists($filter, $getMethodDefaults($methodDefaults))) {
                    $args[$filter] = $getMethodDefaults($methodDefaults)[$filter];
                }

                $checkValue[] = $args[$filter];
            }

            if (array_intersect($filters, array_keys($args)) !== $filters) {
                throw new AclessException('Список параметров выполнения действия не содержит все фильтрующие параметры');
            }

            if (
                ($accessRight['is_allow'] && !\in_array($checkValue, $accessRight['values'], true))
                || (!$accessRight['is_allow'] && \in_array($checkValue, $accessRight['values'], true))
            ) {
                throw new AclessException("Выполнение метода с такими параметрами $filters Вам не разрешено");
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
     * @throws AclessException
     * @throws RedisSingletonException
     */
    public function checkFileRights(string $filePath): bool
    {
        if (empty($accessRight = $this->getAccessRights($filePath))) {
            if ($this->getUserId() === $this->getConfig('guest_user_id')) {
                throw new AclessException('Авторизуйтесь на сайте', self::ACLESS_UNAUTH_ERROR_CODE);
            }

            throw new AclessException('Файл Вам не доступен', self::ACLESS_403_ERROR_CODE);
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
     * @throws AclessException
     */
    public function methodToURL(string $className, string $methodName): string
    {
        foreach ($this->config['controllers'] as $controllerDir) {
            if (empty($controllerDir['path'])) {
                throw new AclessException('Неверный формат данных о директории с контроллерами: нет необходимого параметра "path"');
            }

            $className = str_replace($controllerDir['namespace'], '', $className);
        }

        $methodName = str_replace('\\', '/', trim($className, '\/ ') . '\\' . trim($methodName, '\/ '));

        return AclessHelper::camel2dashed(preg_replace('(Controller|action)', '', $methodName));
    }

    /**
     * Сгенерировать массив урлов контроллеров
     *
     * @param string $controllerFileName - имя файла контроллера
     * @param string|null $controllerNamespace - пространство имен для конроллера, если есть
     *
     * @return array
     *
     * @throws AclessException
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
        $sql = 'SELECT id, schema_name, table_name FROM acless.model_type';
        $models = $this->getPSConnection()->query($sql)->fetchAll(\PDO::FETCH_COLUMN);

        $seachModel = function (string $schema, string $table) use ($models): ?int {
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
            if (empty($docBlock = new DocBlock($method)) || empty($docBlock->getTagsByName($this->config['acless_label']))) {
                continue;
            }

            if ($method->getDeclaringClass()->getName() === 'avtomon\AclessControllerParent') {
                continue;
            }

            $methodName = str_replace('action', '', $method->getName());

            $aclessSchema = $docBlock->getTagsByName($this->config['acless_schema']);
            $aclessSchema = end($aclessSchema);
            $aclessTables = $docBlock->getTagsByName($this->config['acless_tables']);
            $aclessTables = end($aclessTables);
            $aclessUrlType = $docBlock->getTagsByName($this->config['acless_url_type']);
            $aclessUrlType = end($aclessUrlType);

            $modelId = $seachModel($aclessSchema, $aclessTables);

            $url = [
                'text' => '/' . strtolower(str_replace('Controller', '', $controller)) . '/' . AclessHelper::camel2dashed($methodName),
                'name' => $docBlock->getSummary(),
                'model_type_id' => $modelId,
                'type' => $aclessUrlType
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
     * @throws AclessException
     * @throws \ReflectionException
     */
    public function getControllerURLs(): array
    {
        if (empty($this->config['controllers'])) {
            return null;
        }

        $urls = [];
        foreach ($this->config['controllers'] as $controllerDir) {
            if (empty($controllerDir['path'])) {
                throw new AclessException('Неверный формат данных о директории с контроллерами: нет необходимого параметра "path"');
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
        if (empty($this->config['files'])) {
            return null;
        }

        $urls = [];
        foreach ($this->config['files'] as $fileDir) {
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
        }, array_filter($this->config['urls']));
    }

    /**
     * Возращает все собранные урлы
     *
     * @return array
     *
     * @throws AclessException
     * @throws \ReflectionException
     */
    public function getAllURLs(): array
    {
        return array_merge($this->getControllerURLs(), $this->getFilesURLs(), $this->getPlainURLs());
    }
}

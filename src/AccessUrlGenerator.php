<?php

namespace Scaleplan\Access;

use phpDocumentor\Reflection\DocBlock;
use Scaleplan\Access\Exceptions\FormatException;
use function Scaleplan\DependencyInjection\get_required_container;
use function Scaleplan\Helpers\get_required_env;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AccessUrlGenerator
 *
 * @package Scaleplan\Access
 */
class AccessUrlGenerator
{
    /**
     * @var AccessAbstract
     */
    protected $access;

    /**
     * @var AccessConfig
     */
    protected $config;

    /**
     * AccessUrlGenerator constructor.
     *
     * @param AccessAbstract $access
     *
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Helpers\Exceptions\EnvNotFoundException
     */
    public function __construct(AccessAbstract $access)
    {
        $this->access = $access;
        $this->config = $access->getConfig();

        $locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']) ?: get_required_env('DEFAULT_LANG');
        /** @var \Symfony\Component\Translation\Translator $translator */
        $translator = get_required_container(TranslatorInterface::class, [$locale]);
        $translator->addResource('yml', __DIR__ . "/translates/$locale/access.yml", $locale, 'access');
    }

    /**
     * @param string $schema
     * @param string $table
     * @param array $models
     *
     * @return int|null
     */
    protected static function searchModelId(string $schema, string $table, array $models) : ?int
    {
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
    }

    /**
     * Сгенерировать массив урлов контроллеров
     *
     * @param string $controllerFileName - имя файла контроллера
     * @param string|null $controllerNamespace - пространство имен для конроллера, если есть
     *
     * @return array
     *
     * @throws Exceptions\ConfigException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    protected function generateControllerURLs(
        string $controllerFileName,
        string $controllerNamespace = null
    ) : array
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
        $models = $this->access->getPSConnection()->query($sql)->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($refClass->getMethods() as $method) {
            $docBlock = new DocBlock($method);
            if (empty($docBlock->getTagsByName($this->config->get('access_label')))) {
                continue;
            }

            if ($method->getDeclaringClass()->getName() === AccessControllerParent::class) {
                continue;
            }

            $methodPrefix = $this->config->get(AccessConfig::CONTROLLERS_SECTION_NAME)['method_prefix'] ?? '';
            $methodName = str_replace($methodPrefix, '', $method->getName());

            $accessSchema = $docBlock->getTagsByName($this->config->get(AccessConfig::ANNOTATION_SCHEMA_LABEL_NAME));
            $accessSchema = end($accessSchema);
            $accessTables = $docBlock->getTagsByName($this->config->get(AccessConfig::ANNOTATION_TABLE_LABEL_NAME));
            $accessTables = end($accessTables);
            $accessUrlType = $docBlock->getTagsByName($this->config->get(AccessConfig::ANNOTATION_URL_TYPE_LABEL_NAME));
            $accessUrlType = end($accessUrlType);

            $modelId = static::searchModelId($accessSchema, $accessTables, $models);

            $url = [
                'text'          => '/' . strtolower(
                        str_replace(
                            'Controller', '', $controller)
                    ) . '/' . AccessHelper::camel2dashed($methodName),
                'name'          => $docBlock->getText(),
                'model_type_id' => $modelId,
                'type'          => $accessUrlType,
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
    protected function getRecursivePaths(string $dir) : array
    {
        $dir = rtrim($dir, '/\ ');
        $paths = scandir($dir, SCANDIR_SORT_NONE);
        unset($paths[0], $paths[1]);
        $result = [];

        foreach ($paths as &$path) {
            if (is_dir("$dir/$path")) {
                $result = \array_merge($result, array_map(static function ($item) use ($path, $dir) {
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
     * @throws Exceptions\ConfigException
     * @throws FormatException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function getControllerURLs() : array
    {
        if (empty($this->config->get(AccessConfig::CONTROLLERS_SECTION_NAME))) {
            return null;
        }

        $urls = [];
        foreach ($this->config->get(AccessConfig::CONTROLLERS_SECTION_NAME) as $controllerDir) {
            if (empty($controllerDir['path'])) {
                throw new FormatException(translate('access.path-missing'));
            }

            $controllers = array_map(static function ($item) use ($controllerDir) {
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
    public function getFilesURLs() : array
    {
        if (empty($this->config->get(AccessConfig::FILES_SECTION_NAME))) {
            return null;
        }

        $urls = [];
        foreach ($this->config->get(AccessConfig::FILES_SECTION_NAME) as $fileDir) {
            $urls = array_merge($urls, array_map(static function ($item) use ($fileDir) {
                return [
                    'text'          => trim(str_replace($fileDir, '', $item), '\/ '),
                    'name'          => null,
                    'model_type_id' => null,
                    'type'          => null,
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
    public function getPlainURLs() : array
    {
        return array_map(static function ($item) {
            return [
                'text'          => trim($item, '\/ '),
                'name'          => null,
                'model_type_id' => null,
                'type'          => null,
            ];
        }, array_filter($this->config->get(AccessConfig::URLS_SECTION_NAME)));
    }

    /**
     * Возращает все собранные урлы
     *
     * @return array
     *
     * @throws Exceptions\ConfigException
     * @throws FormatException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function getAllURLs() : array
    {
        return array_merge($this->getControllerURLs(), $this->getFilesURLs(), $this->getPlainURLs());
    }
}

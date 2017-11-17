<?php

namespace avtomon;

use phpDocumentor\Reflection\DocBlockFactory;
use Symfony\Component\Yaml\Yaml;

class AclessException extends \Exception
{
}

class Acless
{
    private $docBlockFactory = null;
    private $config = null;

    public function __construct()
    {
        $this->docBlockFactory = DocBlockFactory::createInstance();
        $this->config = Yaml::parse(file_get_contents(__DIR__ . '/config.yml'));
    }

    public function getConfig()
    {
        return $this->config;
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

    /**
     * Сгенерировать массив урлов контроллеров
     *
     * @param string $controllerFileName - имя файла контроллера
     * @param string|null $controllerNamespace - пространство имен для конроллера, если есть
     *
     * @return array
     */
    private function generateControllerURLs(string $controllerFileName, string $controllerNamespace = null): array
    {
        $controller = trim(explode('.', $controllerFileName)[0]);
        $controllerNamespace = trim($controllerNamespace, '/\ ');
        if ($controllerNamespace) {
            $controllerNamespace = "\\$controllerNamespace\\";
            $controller = str_replace('/', '\\', $controller);
        } else {
            $controller = end(explode('/', $controller));
        }

        $refClass = new \ReflectionClass("$controllerNamespace$controller");

        $urls = [];
        foreach ($refClass->getMethods() as $method) {
            $url = [
                'text' => '/' .
                strtolower(str_replace('Controller', '', $controller)) .
                '/' .
                self::camel2dashed(str_replace('action', '', $method->getName())),
                'name' => null,
                'filter' => null,
                'filter_reference' => null
            ];
            if ($method->getDocComment()) {
                $docBlock = $this->docBlockFactory->create($method->getDocComment());
                $url['name'] = $docBlock->getSummary();
                $acless = $docBlock->getTagsByName('acless');
                $pr = '[\w\d_\-\.]+';
                if (end($acless) && preg_match("/^\\\$($pr)\s*\->\s*($pr\.$pr\.$pr)$/i", end($acless)->getDescription()->render(), $mathches)) {
                    $url['filter'] = $mathches[1];
                    $url['filter_reference'] = $mathches[2];
                }
            }

            array_push($urls, $url);
        }

        return $urls;
    }

    /**
     * Найти все файлы в каталоге, включая вложенные директории
     *
     * @param string $dir
     *
     * @return array
     */
    private function getRecursivePaths(string $dir): array
    {
        $dir = rtrim($dir, '/\ ');
        $paths = scandir($dir);
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
                    'filter' => null,
                    'filter_reference' => null
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
                'filter' => null,
                'filter_reference' => null
            ];
        }, $this->config['urls'] ?? []);
    }

    /**
     * Возращает все собранные урлы
     *
     * @return array
     */
    public function getAllURLs(): array
    {
        return array_merge($this->getControllerURLs(), $this->getFilesURLs(), $this->getPlainURLs());
    }
}
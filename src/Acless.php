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
        $this->config = Yaml::parse(file_get_contents('config.yml'));
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
            $url = [];
            if ($method->getDocComment()) {
                $docBlock = $this->docBlockFactory->create($method->getDocComment());
                $url['name'] = $docBlock->getSummary();
                foreach ($docBlock->getTags() as $param) {
                    if ($param->getName() === 'param' && preg_match('/\(acless_filter:\s*([\w\d_\-\.]+)\)\s*\-\s*(.+)/i', $param->getDescription()->render(), $mathches)) {
                        $url['filter'] = $param->getVariableName();
                        $url['filter_reference'] = $mathches[1];
                        $url['filter_name'] = $mathches[2];
                    }
                }
            }

            $url['text'] = '/' .
                strtolower(str_replace('Controller', '', $controller)) .
                '/' .
                self::camel2dashed(str_replace('action', '', $method->getName()));

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
        $paths = scandir($dir);
        unset($paths[0], $paths[1]);
        $result = $paths;

        foreach ($paths as $index => $path) {
            if (is_dir($path)) {
                unset($result[$index]);
                $result = array_merge($result, array_map(function ($item) use ($path) {
                    return "$path\\$item";
                }, $this->getRecursivePaths(trim($dir, '/\ ') . '\\' . $path)));
            }
        }

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
        if (isset($this->config['controllers']) && count($this->config['controllers'])) {
            return null;
        }

        $urls = [];
        foreach ($this->config['controllers'] as $controllerDir) {
            if (!isset($controllerDir['path']) || !trim($controllerDir['path'])) {
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
        if (isset($this->config['files']) && count($this->config['files'])) {
            return null;
        }

        $urls = [];
        foreach ($this->config['files'] as $fileDir) {
            $urls = array_merge($urls, array_map(function ($item) use ($fileDir) {
                return trim(str_replace($fileDir, '', $item), '\/ ');
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
        return $this->config['urls'];
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
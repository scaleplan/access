<?php

namespace avtomon;

class AclessException extends \Exception
{
}

/**
 * Класс формирования списка урлов и проверки прав
 *
 * Class Acless
 * @package avtomon
 */
class Acless extends AbstractAcless
{
    private $docBlockFactory = null; // Фабрика phpdoc-блоков

    /**
     * Вернуть информацию о всех доступных пользователю урлах или о каком-то конкретном урле
     *
     * @param string|null $url - текст урла
     *
     * @return array
     *
     * @throws AclessException
     */
    private function getAccessRights(string $url = null): array
    {
        switch ($this->config['cache_storage']) {
            case 'redis':
                if (empty($this->config['redis']['socket'])) {
                    throw new AclessException('В конфигурации не задан путь к Redis-сокету');
                }

                $this->cs = $this->cs ?? RedisSingleton::create($this->config['redis']['socket']);
                if ($url) {
                    return $redis->hGet("user:{$this->userId}", $url) ?? [];
                } else {
                    return $redis->hGetAll("user:{$this->userId}") ?? [];
                }

                break;

            case 'session':
                return $_SESSION['access_rights'][$url] ?? [];
                break;

            default:
                throw new AclessException("Драйвер {$config['cache_storage']} кэширующего хранилища не поддерживается системой");
        }
    }

    /**
     * Проверить доступ к методу
     *
     * @param \Reflector $ref - Reflection-обертка для метода
     *
     * @return bool
     *
     * @throws AclessException
     */
    public function checkMethodRights(\Reflector $ref): bool
    {
        $url = $this->methodToURL(static::class, $ref->getName());
        if (!($accessRight = $this->getAccessRights($url))) {
            throw new AclessException('Метод не разрешен Вам для выпонения');
        }

        if (!($docBlock = $this->docBlockFactory->create($ref->getDocComment()) || !($docParam = end($docBlock->getTagsByName('acless'))))) {
            return true;
        }

        $filter = trim($docParam->getDescription()->render());
        if ($filter && in_array($filter, $args)) {
            if (($accessRight['is_allow'] && !in_array($args[$filter], $accessRight['values'])) || (!$accessRight['is_allow'] && in_array($args[$filter], $accessRight['values']))) {
                throw new AclessException("Выполнение метода с таким параметром $filter Вам не разрешено");
            }
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
    public function methodToURL(string $className, string $methodName)
    {
        $methodName = str_replace('\\', '/', trim($className, '\/ ') . '\\' . trim($methodName, '\/ '));
        foreach ($this->config['controllers'] as $controllerDir) {
            if (empty($controllerDir['path'])) {
                throw new AclessException('Неверный формат данных о директории с контроллерами: нет необходимого параметра "path"');
            }

            str_replace($controllerDir['path'], '', $methodName);
        }

        return AclessHelper::camel2dashed(str_replace('Controller', '', $methodName));
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
                AclessHelper::camel2dashed(str_replace('action', '', $method->getName())),
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
     * @param string $dir - путь к каталогу
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

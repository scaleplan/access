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
class Acless extends AclessAbstract
{
    /**
     * Фабрика phpdoc-блоков
     *
     * @var null
     */
    public $docBlockFactory = null;

    protected static $instance = null;

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
                    throw new AclessException('В конфигурации не задан путь к Redis-сокету', 41);
                }

                $this->cs = $this->cs ?? RedisSingleton::create($this->config['redis']['socket']);
                if ($url) {
                    return json_decode($this->cs->hGet("user_id:{$this->userId}", $url), true)  ?? [];
                } else {
                    return array_map(function ($item) {
                        return json_decode($item, true) || $item;
                    }, array_filter($this->cs->hGetAll("user_id:{$this->userId}")));
                }

                break;

            case 'session':
                return $url ? ($_SESSION['access_rights'][$url] ?? []) : array_filter($_SESSION['access_rights']);
                break;

            default:
                throw new AclessException("Драйвер {$config['cache_storage']} кэширующего хранилища не поддерживается системой", 42);
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
        if (empty($docBlock = $this->docBlockFactory->create($ref->getDocComment())) || empty($tag = $docBlock->getTagsByName($this->config['acless_label']))) {
            return true;
        }

        $url = $this->methodToURL($ref->getDeclaringClass()->getName(), $ref->getName());
        if (empty($accessRight = $this->getAccessRights($url))) {
            if ($this->getUserId() === $this->getConfig('guest_user_id')) {
                throw new AclessException('Авторизуйтесь на сайте', 47);
            }

            throw new AclessException('Метод не разрешен Вам для выпонения', 43);
        }

        if (empty($tag = $docBlock->getTagsByName($this->config['filter_label']))) {
            return true;
        }

        $docParam = end($tag);
        $filter = trim($docParam->getDescription() ? $docParam->getDescription()->render() : '');
        if ($filter && in_array($filter, $args)) {
            if (($accessRight['is_allow'] && !in_array($args[$filter], $accessRight['values'])) || (!$accessRight['is_allow'] && in_array($args[$filter], $accessRight['values']))) {
                throw new AclessException("Выполнение метода с таким параметром $filter Вам не разрешено", 44);
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
     */
    public function checkFileRights(string $filePath): bool
    {
        if (empty($accessRight = $this->getAccessRights($filePath))) {
            if ($this->getUserId() === $this->getConfig('guest_user_id')) {
                throw new AclessException('Авторизуйтесь на сайте', 47);
            }

            throw new AclessException('Файл Вам не доступен', 43);
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
        foreach ($this->config['controllers'] as $controllerDir) {
            if (empty($controllerDir['path'])) {
                throw new AclessException('Неверный формат данных о директории с контроллерами: нет необходимого параметра "path"', 45);
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
     */
    private function generateControllerURLs(string $controllerFileName, string $controllerNamespace = null): array
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
        foreach ($refClass->getMethods() as $method) {
            if (empty($doc = $method->getDocComment()) || empty($docBlock = $this->docBlockFactory->create($method->getDocComment())) || empty($docBlock->getTagsByName($this->config['acless_label'])))
            {
                continue;
            }

            $url = [
                'text' => '/' .
                strtolower(str_replace('Controller', '', $controller)) .
                '/' .
                AclessHelper::camel2dashed(str_replace('action', '', $method->getName())),
                'name' => $docBlock->getSummary(),
                'filter' => null,
                'filter_reference' => null
            ];
            if ($filterField = $docBlock->getTagsByName($this->config['filter_label'])) {
                $pr = '[\w\d_\-\.]+';
                $filterField = end($filterField);
                if ($filterField && preg_match(
                    "/^\\\$($pr)\s*\(?:->)*\s*($pr\.$pr\.$pr)*\s*$/i",
                    $filterField->getDescription() ? $filterField->getDescription()->render() : '',
                    $mathches
                    )
                ) {
                    $url['filter'] = $mathches[1];
                    if (!empty($mathches[2])) {
                        $url['filter_reference'] = $mathches[2];
                    }
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
                throw new AclessException('Неверный формат данных о директории с контроллерами: нет необходимого параметра "path"', 46);
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
        }, array_filter($this->config['urls']));
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

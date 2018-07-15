<?php

namespace avtomon;

use Symfony\Component\Yaml\Yaml;

/**
 * Суперкласс
 *
 * Class AbstractAcless
 * @package avtomon
 */
abstract class AclessAbstract
{
    /**
     * Конфигурация
     *
     * @var array
     */
    protected $config = [];

    /**
     * Идентификатор пользователя
     *
     * @var int
     */
    protected $userId = 0;

    /**
     * Подключение к РСУБД
     *
     * @var null|\PDO
     */
    protected $ps;

    /**
     * Подключение к кэшу
     *
     * @var null|\Redis
     */
    protected $cs;

    /**
     * Инстанс класса
     *
     * @var null|AclessAbstract
     */
    protected static $instance;

    /**
     * Синглтон
     *
     * @param int $userId - идентификатор пользователя
     * @param string $confPath - путь в файлу конфигурации
     *
     * @return AclessAbstract
     */
    public static function create(int $userId = -1, string $confPath = __DIR__ . '../config.yml'): AclessAbstract
    {
        if (!static::$instance) {
            $className = static::class;
            static::$instance = new $className($userId, $confPath);
        }

        return static::$instance;
    }

    /**
     * AclessAbstract constructor
     *
     * @param int $userId - идентификатор пользователя
     * @param string $confPath - пусть к конфигурации
     *
     * @throws AclessException
     */
    private function __construct(int $userId, string $confPath)
    {
        $this->config = Yaml::parse(file_get_contents($confPath));

        if (empty($this->config)) {
            throw new AclessException('Отсутствует конфигурация');
        }

        if (empty($this->config['persistent_storage'])) {
            throw new AclessException('В конфирурациии отсутствует указание постоянного хранилища');
        }

        if (empty($this->config[$this->config['persistent_storage']])){
            throw new AclessException('В конфигурации отсутствуют данные о подключениие к постоянному хранилищу прав');
        }

        if (empty($this->config['cache_storage'])) {
            throw new AclessException('В конфирурациии отсутствует указание кефирующего хранилища');
        }

        if (empty($this->config[$this->config['cache_storage']])){
            throw new AclessException('В конфигурации отсутствуют данные о подключениие к кэширующему хранилищу прав');
        }

        if (!\is_array($this->config['roles'])){
            throw new AclessException('Список ролей должен быть задан списком');
        }

        if (empty($this->config['acless_filter_label'])) {
            throw new AclessException('В конфигурации отсутствует имя для метки фильтрующего аргумента');
        }

        if (empty($this->config['acless_label'])) {
            throw new AclessException('В конфигурации отсутствует имя метки Acless');
        }

        if (empty($this->config['acless_separator'])) {
            throw new AclessException('В конфигурации отсутствует разделитель фильтров');
        }

        if ($userId < 0) {
            throw new AclessException('Неверное задан идентификатор пользователя');
        }

        $this->userId = $userId;
    }

    /**
     * Возвращает идентификатор пользователя
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Вернуть подключение в РСУБД
     *
     * @return \PDO
     *
     * @throws AclessException
     */
    protected function getPSConnection(): \PDO
    {
        if ($this->ps) {
            return $this->ps;
        }

        switch ($this->config['persistent_storage']) {
            case 'postgresql':
                if (empty($this->config['postgresql']) || empty($this->config['postgresql']['dns']) || empty($this->config['postgresql']['user']) || empty($this->config['postgresql']['password'])) {
                    throw new AclessException('Недостаточно данных для подключения к PostgreSQL');
                }

                $this->ps = $this->ps ?? new \PDO(
                        $this->config['postgresql']['dns'],
                        $this->config['postgresql']['user'],
                        $this->config['postgresql']['password']
                    );
                $this->ps->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->ps->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_TO_STRING);
                $this->ps->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

                return $this->ps;

            default:
                throw new AclessException("Драйвер {$this->config['persistent_storage']} постоянного хранилища не поддерживается системой");
        }
    }

    /**
     * Вернуть конфигурацию или ее часть
     *
     * @param string|null $key - ключ конфигурации
     *
     * @return array|mixed|null
     */
    public function getConfig(string $key = null)
    {
        return $key ? $this->config[$key] ?? null : $this->config;
    }
}
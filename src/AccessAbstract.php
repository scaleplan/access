<?php

namespace Scaleplan\Access;

use Scaleplan\Access\Constants\ConfigConstants;
use Scaleplan\Access\Exceptions\ConfigException;
use Symfony\Component\Yaml\Yaml;

/**
 * Суперкласс
 *
 * Class AccessAbstract
 *
 * @package Scaleplan\Access
 */
abstract class AccessAbstract
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
     * @var null|AccessAbstract
     */
    protected static $instance;

    /**
     * Синглтон
     *
     * @param int $userId - идентификатор пользователя
     * @param string $confPath - путь в файлу конфигурации
     *
     * @return AccessAbstract
     */
    public static function create(int $userId = -1, string $confPath = __DIR__ . '/../config.yml'): AccessAbstract
    {
        if (!static::$instance) {
            $className = static::class;
            static::$instance = new $className($userId, $confPath);
        }

        return static::$instance;
    }

    /**
     * AccessAbstract constructor.
     *
     * @param int $userId - идентификатор пользователя
     * @param string $confPath - пусть к конфигурации
     *
     * @throws ConfigException
     */
    private function __construct(int $userId, string $confPath)
    {
        $this->config = Yaml::parse(file_get_contents($confPath));

        if (empty($this->config)) {
            throw new ConfigException('Отсутствует конфигурация');
        }

        if (empty($this->config[ConfigConstants::PERSISTENT_STORAGE_SECTION_NAME])) {
            throw new ConfigException('В конфирурациии отсутствует указание постоянного хранилища');
        }

        if (empty($this->config[$this->config[ConfigConstants::PERSISTENT_STORAGE_SECTION_NAME]])){
            throw new ConfigException(
                'В конфигурации отсутствуют данные о подключениие к постоянному хранилищу прав'
            );
        }

        if (empty($this->config[ConfigConstants::CACHE_STORAGE_SECTION_NAME])) {
            throw new ConfigException('В конфирурациии отсутствует указание кефирующего хранилища');
        }

        if (empty($this->config[$this->config[ConfigConstants::CACHE_STORAGE_SECTION_NAME]])){
            throw new ConfigException(
                'В конфигурации отсутствуют данные о подключениие к кэширующему хранилищу прав'
            );
        }

        if (!isset($this->config[ConfigConstants::ROLES_SECTION_NAME])){
            throw new ConfigException('Отсутствует список ролей');
        }

        if (!\is_array($this->config[ConfigConstants::ROLES_SECTION_NAME])){
            throw new ConfigException('Список ролей должен быть задан списком');
        }

        if (empty($this->config[ConfigConstants::FILTER_DIRECTIVE_NAME])) {
            throw new ConfigException('В конфигурации отсутствует имя для метки фильтрующего аргумента');
        }

        if (empty($this->config[ConfigConstants::ANNOTATION_LABEL_NAME])) {
            throw new ConfigException('В конфигурации отсутствует имя метки Access');
        }

        if (empty($this->config[ConfigConstants::FILTER_SEPARATOR_NAME])) {
            throw new ConfigException('В конфигурации отсутствует разделитель фильтров');
        }

        if ($userId < 0) {
            throw new ConfigException('Неверное задан идентификатор пользователя');
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
     * @throws ConfigException
     */
    protected function getPSConnection(): \PDO
    {
        if ($this->ps) {
            return $this->ps;
        }

        $persistentStorageName = $this->config[ConfigConstants::PERSISTENT_STORAGE_SECTION_NAME];
        switch ($persistentStorageName) {
            case 'postgresql':
                $postgresSection = &$this->config[$persistentStorageName] ?? null;
                if (empty($postgresSection)
                    ||
                    empty($postgresSection['dns'])
                    ||
                    empty($postgresSection['user'])
                    ||
                    empty($postgresSection['password'])
                ) {
                    throw new ConfigException('Недостаточно данных для подключения к PostgreSQL');
                }

                $this->ps = $this->ps ?? new \PDO(
                        $postgresSection['dns'],
                        $postgresSection['user'],
                        $postgresSection['password']
                    );
                $this->ps->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->ps->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_TO_STRING);
                $this->ps->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

                return $this->ps;

            default:
                throw new ConfigException(
                    "Драйвер {$persistentStorageName} постоянного хранилища не поддерживается системой"
                );
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
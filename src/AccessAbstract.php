<?php

namespace Scaleplan\Access;

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
    protected const DEFAULT_USER_ID = -1;
    /**
     * Конфигурация
     *
     * @var AccessConfig
     */
    protected $config;

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
    public static function create(
        \int $userId = self::DEFAULT_USER_ID,
        \string $confPath = __DIR__ . '/../config.yml'
    ) : AccessAbstract
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
        $this->config = new AccessConfig(Yaml::parse(file_get_contents($confPath)));

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
    public function getUserId() : int
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
    public function getPSConnection() : \PDO
    {
        if ($this->ps) {
            return $this->ps;
        }

        $persistentStorageName = $this->config->get(AccessConfig::PERSISTENT_STORAGE_SECTION_NAME);
        switch ($persistentStorageName) {
            case 'postgresql':
                $postgresSection = $this->config->get($persistentStorageName);
                if (empty($postgresSection) || empty($postgresSection['dns'])
                    || empty($postgresSection['user']) || empty($postgresSection['password'])) {
                    throw new ConfigException('Недостаточно данных для подключения к PostgreSQL');
                }

                $this->ps = $this->ps
                    ?? new \PDO($postgresSection['dns'], $postgresSection['user'], $postgresSection['password']);

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
     * @return AccessConfig
     */
    public function getConfig() : AccessConfig
    {
        return $this->config;
    }
}

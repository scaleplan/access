<?php

namespace Scaleplan\Access;

use Scaleplan\Access\CacheStorage\CacheStorageFabric;
use Scaleplan\Access\CacheStorage\CacheStorageInterface;
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
     * @var CacheStorageInterface
     */
    protected $cache;

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
    ) : self
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
     * @throws Exceptions\CacheTypeNotSupportingException
     * @throws Exceptions\UserIdNotPresentException
     */
    private function __construct(int $userId, string $confPath)
    {
        $this->config = new AccessConfig(Yaml::parse(file_get_contents($confPath)));
        $this->cache = CacheStorageFabric::getInstance(
            $this->config->get(AccessConfig::CACHE_STORAGE_SECTION_NAME),
            $userId
        );

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
        static $connection;
        if (!$connection) {
            $connectionData = $this->config->get(AccessConfig::CACHE_STORAGE_SECTION_NAME);
            $type = $connectionData['type'];
            switch ($type) {
                case 'postgresql':
                    $connection
                        = new \PDO($connectionData['dns'], $connectionData['user'], $connectionData['password']);

                    $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    $connection->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_TO_STRING);
                    $connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);;

                default:
                    throw new ConfigException(
                        "Драйвер $type постоянного хранилища не поддерживается системой"
                    );
            }
        }

        return $connection;
    }

    /**
     * @return AccessConfig
     */
    public function getConfig() : AccessConfig
    {
        return $this->config;
    }
}

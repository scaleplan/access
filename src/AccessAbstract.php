<?php

namespace Scaleplan\Access;

use Scaleplan\Access\CacheStorage\CacheStorageFabric;
use Scaleplan\Access\CacheStorage\CacheStorageInterface;
use Scaleplan\Access\Exceptions\ConfigException;
use function Scaleplan\DependencyInjection\get_required_container;
use function Scaleplan\Helpers\get_required_env;
use function Scaleplan\Translator\translate;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Суперкласс
 *
 * Class AccessAbstract
 *
 * @package Scaleplan\Access
 */
abstract class AccessAbstract
{
    protected const DEFAULT_USER_ID = 0;
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
    protected $userId = self::DEFAULT_USER_ID;

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
    public static function getInstance(
        int $userId = self::DEFAULT_USER_ID,
        string $confPath = __DIR__ . '/../config.yml'
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
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Helpers\Exceptions\EnvNotFoundException
     */
    protected function __construct(int $userId, string $confPath)
    {
        $this->config = new AccessConfig(Yaml::parse(file_get_contents($confPath)));
        $this->cache = CacheStorageFabric::getInstance(
            $this->config,
            $userId
        );

        if ($userId < self::DEFAULT_USER_ID) {
            throw new ConfigException(translate('access.incorrect-user-id'));
        }

        $this->userId = $userId;

        $locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']) ?: get_required_env('DEFAULT_LANG');
        /** @var \Symfony\Component\Translation\Translator $translator */
        $translator = get_required_container(TranslatorInterface::class, [$locale]);
        $translator->addResource('yml', __DIR__ . "/translates/$locale/access.yml", $locale, 'access');
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
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function getPSConnection() : \PDO
    {
        static $connection;
        if (!$connection) {
            $connectionData = $this->config->get(AccessConfig::PERSISTENT_STORAGE_SECTION_NAME);
            $type = &$connectionData['type'];
            switch ($type) {
                case 'postgresql':
                    $connection
                        = new \PDO($connectionData['dns'], $connectionData['user'], $connectionData['password']);

                    $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    $connection->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_TO_STRING);
                    $connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
                    break;

                default:
                    throw new ConfigException(translate('access.incorrect-db-driver', [':driver' => $type]));
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

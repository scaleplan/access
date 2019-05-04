<?php

namespace Scaleplan\Access;

use Scaleplan\Access\CacheStorage\CacheStorageFabric;
use Scaleplan\Access\CacheStorage\CacheStorageInterface;
use Scaleplan\Access\Exceptions\ConfigException;
use Scaleplan\Db\Interfaces\DbInterface;
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
     * @var DbInterface
     */
    protected $storage;

    /**
     * @var string
     */
    protected $confPath;

    /**
     * Синглтон
     *
     * @param DbInterface $storage
     * @param int $userId - идентификатор пользователя
     * @param string $confPath - путь в файлу конфигурации
     *
     * @return AccessAbstract
     */
    public static function getInstance(
        DbInterface $storage,
        int $userId = self::DEFAULT_USER_ID,
        string $confPath = __DIR__ . '/../config.yml'
    ) : self
    {
        if (!static::$instance) {
            $className = static::class;
            static::$instance = new $className($storage, $userId, $confPath);
        }

        return static::$instance;
    }

    /**
     * AccessAbstract constructor.
     *
     * @param DbInterface $storage
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
    protected function __construct(DbInterface $storage, int $userId, string $confPath)
    {
        $this->confPath = $confPath;
        $this->config = new AccessConfig(Yaml::parse(file_get_contents($confPath)));
        $this->cache = CacheStorageFabric::getInstance(
            $this->config,
            $userId
        );

        if ($userId < self::DEFAULT_USER_ID) {
            throw new ConfigException(translate('access.incorrect-user-id'));
        }

        $this->userId = $userId;
        $this->storage = $storage;

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
     */
    public function getPSConnection() : \PDO
    {
        return $this->storage->getConnection();
    }

    /**
     * @return AccessConfig
     */
    public function getConfig() : AccessConfig
    {
        return $this->config;
    }
}

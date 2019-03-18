<?php

namespace Scaleplan\Access\CacheStorage;

use Scaleplan\Access\AccessConfig;
use Scaleplan\Access\Constants\DbConstants;
use Scaleplan\Access\Exceptions\AccessException;
use Scaleplan\Access\Exceptions\ConfigException;
use Scaleplan\Redis\RedisSingleton;

class RedisCache implements CacheStorageInterface
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var array
     */
    protected $cacheData;

    /**
     * RedisCache constructor.
     *
     * @param int $userId
     * @param AccessConfig $config
     */
    public function __construct(\int $userId, AccessConfig $config)
    {
        $this->userId = $userId;
        $this->cacheData = $config->get('redis');
    }

    /**
     * @return \Redis
     *
     * @throws ConfigException
     * @throws \Scaleplan\Redis\Exceptions\RedisSingletonException
     */
    protected function getConnection() : \Redis
    {
        static $connection;
        if (!$connection) {
            if (empty($this->cacheData['socket'])) {
                throw new ConfigException('В конфигурации не задан путь к Redis-сокету');
            }

            $connection = RedisSingleton::create($this->cacheData['socket']);
        }

        return $connection;
    }

    /**
     * @return array
     *
     * @throws ConfigException
     * @throws \Scaleplan\Redis\Exceptions\RedisSingletonException
     */
    public function getAllAccessRights() : array
    {
        return array_map(function ($item) {
            return json_decode($item, true) ?? $item;
        }, array_filter($this->getConnection()->hGetAll(DbConstants::USER_ID_FIELD_NAME . ":{$this->userId}")));
    }

    /**
     * @param string $url
     *
     * @return array
     *
     * @throws ConfigException
     * @throws \Scaleplan\Redis\Exceptions\RedisSingletonException
     */
    public function getAccessRight(string $url) : array
    {
        return json_decode(
                $this->getConnection()->hGet(DbConstants::USER_ID_FIELD_NAME . ":{$this->userId}", $url), true
            ) ?? [];
    }

    /**
     * @param string $url
     *
     * @return array
     *
     * @throws ConfigException
     * @throws \Scaleplan\Redis\Exceptions\RedisSingletonException
     */
    public function getForbiddenSelectors(string $url) : array
    {
        $accessRights = $this->getAccessRight($url);
        return $accessRights[DbConstants::FORBIDDEN_SELECTORS_FIELD_NAME] ?? [];
    }

    /**
     * @param array $accessRights
     *
     * @throws AccessException
     * @throws ConfigException
     * @throws \Scaleplan\Redis\Exceptions\RedisSingletonException
     */
    public function saveToCache(array $accessRights) : void
    {
        $this->getConnection()->delete(DbConstants::USER_ID_FIELD_NAME . ":{$this->userId}");
        $hashValue = array_map(function ($item) {
            return json_encode($item, JSON_FORCE_OBJECT) ?? $item;
        }, array_column($accessRights, null, DbConstants::URL_FIELD_NAME));

        if (!$this->getConnection()->hMSet(DbConstants::USER_ID_FIELD_NAME . ":{$this->userId}", $hashValue)) {
            throw new AccessException('Не удалось записать права доступа в Redis');
        }
    }
}

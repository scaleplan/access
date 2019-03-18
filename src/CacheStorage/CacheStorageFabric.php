<?php

namespace Scaleplan\Access\CacheStorage;

use Scaleplan\Access\AccessConfig;
use Scaleplan\Access\Exceptions\CacheDataEmptyException;
use Scaleplan\Access\Exceptions\CacheTypeNotSupportingException;
use Scaleplan\Access\Exceptions\UserIdNotPresentException;

/**
 * Class CacheStorageFabric
 *
 * @package Scaleplan\Access\CacheStorage
 */
class CacheStorageFabric
{
    /**
     * @var array
     */
    protected static $instances = [];

    /**
     * @param AccessConfig $config
     * @param int|null $userId
     *
     * @return CacheStorageInterface
     *
     * @throws CacheTypeNotSupportingException
     * @throws UserIdNotPresentException
     */
    public static function getInstance(
        AccessConfig $config,
        \int $userId = null
    ) : CacheStorageInterface
    {
        $cacheType = $config->get($config[AccessConfig::CACHE_STORAGE_SECTION_NAME]['type']);
        if (!empty(static::$instances[$cacheType][$userId])) {
            return static::$instances[$cacheType][$userId];
        }

        switch ($cacheType) {
            case 'redis':
                if (!$userId) {
                    throw new UserIdNotPresentException();
                }

                return new RedisCache($userId, $config);

            case 'session':
                return new SessionCache();

            default:
                throw new CacheTypeNotSupportingException();
        }
    }
}

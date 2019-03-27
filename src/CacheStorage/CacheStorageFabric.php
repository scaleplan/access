<?php

namespace Scaleplan\Access\CacheStorage;

use Scaleplan\Access\AccessConfig;
use Scaleplan\Access\Exceptions\CacheTypeNotSupportingException;

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
     */
    public static function getInstance(
        AccessConfig $config,
        int $userId = null
    ) : CacheStorageInterface
    {
        $cacheType = $config->get(AccessConfig::CACHE_STORAGE_SECTION_NAME)['type'] ?? null;
        if (!empty(static::$instances[$cacheType][$userId])) {
            return static::$instances[$cacheType][$userId];
        }

        switch ($cacheType) {
            case 'redis':
                return new RedisCache($userId, $config);

            case 'session':
                return new SessionCache();

            default:
                throw new CacheTypeNotSupportingException();
        }
    }
}

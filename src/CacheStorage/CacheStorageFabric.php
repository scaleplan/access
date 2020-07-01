<?php
declare(strict_types=1);

namespace Scaleplan\Access\CacheStorage;

use Scaleplan\Access\AccessConfig;
use Scaleplan\Access\Exceptions\CacheTypeNotSupportingException;
use function Scaleplan\Translator\translate;

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
     * @param string|null $database
     *
     * @return CacheStorageInterface
     *
     * @throws CacheTypeNotSupportingException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public static function getInstance(
        AccessConfig $config,
        int $userId = null,
        string $database = null
    ) : CacheStorageInterface
    {
        $cacheType = $config->get(AccessConfig::CACHE_STORAGE_SECTION_NAME)['type'] ?? null;
        if (!empty(static::$instances[$cacheType][$userId])) {
            return static::$instances[$cacheType][$userId];
        }

        switch ($cacheType) {
            case 'redis':
                return new RedisCache($userId, $config, $database);

            case 'session':
                return new SessionCache();

            default:
                throw new CacheTypeNotSupportingException(
                    translate('access.cache-type-not-supporting', ['type' => $cacheType])
                );
        }
    }
}

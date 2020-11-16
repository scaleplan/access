<?php
declare(strict_types=1);

namespace Scaleplan\Access\CacheStorage;

/**
 * Interface CacheStorageInterface
 *
 * @package Scaleplan\Access\CacheStorage
 */
interface CacheStorageInterface
{
    /**
     * @param string $url
     *
     * @return array
     */
    public function getAccessRight(string $url) : array;

    /**
     * @return array|null
     */
    public function getAllAccessRights() : ?array;

    /**
     * @param string $url
     * @param array $args
     *
     * @return array
     */
    public function getForbiddenSelectors(string $url, array $args) : array;

    /**
     * @param array $accessRights
     */
    public function saveToCache(array $accessRights) : void;

    public function flush() : void;
}

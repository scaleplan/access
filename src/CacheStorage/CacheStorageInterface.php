<?php

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
    public function getAccessRight(\string $url) : array;

    /**
     * @return array
     */
    public function getAllAccessRights() : array;

    /**
     * @param string $url
     *
     * @return array
     */
    public function getForbiddenSelectors(\string $url) : array;

    /**
     * @param array $accessRights
     */
    public function saveToCache(array $accessRights) : void;
}

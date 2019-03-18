<?php

namespace Scaleplan\Access\CacheStorage;

use Scaleplan\Access\Constants\DbConstants;
use Scaleplan\Access\Constants\SessionConstants;

/**
 * Class SessionCache
 *
 * @package Scaleplan\Access\CacheStorage
 */
class SessionCache implements CacheStorageInterface
{
    /**
     * @param string $url
     *
     * @return array
     */
    public function getAccessRight(string $url) : array
    {
        return $_SESSION[SessionConstants::SESSION_ACCESS_RIGHTS_SECTION_NAME][$url] ?? [];
    }

    /**
     * @return array
     */
    public function getAllAccessRights() : array
    {
        return $_SESSION[SessionConstants::SESSION_ACCESS_RIGHTS_SECTION_NAME] ?? [];
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public function getForbiddenSelectors(string $url) : array
    {
        return $_SESSION[SessionConstants::SESSION_ACCESS_RIGHTS_SECTION_NAME]
            [$url][DbConstants::FORBIDDEN_SELECTORS_FIELD_NAME] ?? [];
    }

    /**
     * @param array $accessRights
     */
    public function saveToCache(array $accessRights) : void
    {
        $_SESSION[SessionConstants::SESSION_ACCESS_RIGHTS_SECTION_NAME]
            = array_column($accessRights, null, 'url');
    }
}

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
        return $this->getAllAccessRights()[$url] ?? [];
    }

    /**
     * @return array
     */
    public function getAllAccessRights() : array
    {
        return (array)($_SESSION[SessionConstants::SESSION_ACCESS_RIGHTS_SECTION_NAME] ?? []);
    }

    /**
     * @param string $url
     * @param array $args
     *
     * @return string[]
     */
    public function getForbiddenSelectors(string $url, array $args) : array
    {
        $accessRights = $this->getAccessRight($url);
        if (empty($accessRights[DbConstants::RIGHTS_FIELD_NAME])) {
            return [];
        }

        $forbiddenSelectors = [];
        foreach ($accessRights[DbConstants::RIGHTS_FIELD_NAME] as $field => $data) {
            if (!array_key_exists($field, $args)) {
                continue;
            }

            $part = $accessRights[DbConstants::RIGHTS_FIELD_NAME][$field][DbConstants::FORBIDDEN_SELECTORS_FIELD_NAME];
            if (!empty($part)) {
                $forbiddenSelectors += array_map('trim', explode(',', $part));
            }
        }

        return array_unique($forbiddenSelectors);
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

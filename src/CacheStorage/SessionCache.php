<?php
declare(strict_types=1);

namespace Scaleplan\Access\CacheStorage;

use Scaleplan\Access\Constants\DbConstants;
use Scaleplan\Access\Constants\SessionConstants;
use Scaleplan\Access\Exceptions\AccessException;
use function Scaleplan\Translator\translate;

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
        return ($this->getAllAccessRights() ?? [])[$url] ?? [];
    }

    /**
     * @return array|null
     */
    public function getAllAccessRights() : ?array
    {
        return $_SESSION[SessionConstants::SESSION_ACCESS_RIGHTS_SECTION_NAME] ?? null;
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

    /**
     * @throws AccessException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function flush() : void
    {
        throw new AccessException(translate('access.flushing-all-rights-not-supporting'));
    }
}

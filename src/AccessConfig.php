<?php

namespace Scaleplan\Access;

use Scaleplan\Access\Exceptions\ConfigException;
use function Scaleplan\Translator\translate;

/**
 * Class AccessConfig
 *
 * @package Scaleplan\Access
 */
class AccessConfig
{
    public const CACHE_STORAGE_SECTION_NAME = 'cache_storage';

    public const GUEST_USER_ID_DIRECTIVE_NAME = 'guest_user_id';

    public const FILTER_DIRECTIVE_NAME = 'access_filter_label';

    public const ANNOTATION_LABEL_NAME = 'access_label';

    public const ANNOTATION_SCHEMA_LABEL_NAME = 'access_schema';

    public const ANNOTATION_TABLE_LABEL_NAME = 'access_table';

    public const ANNOTATION_URL_TYPE_LABEL_NAME = 'access_url_type';

    public const FILES_SECTION_NAME = 'files';

    public const URLS_SECTION_NAME = 'urls';

    public const CONTROLLERS_SECTION_NAME = 'controllers';

    public const ROLES_SECTION_NAME = 'roles';

    public const DEFAULT_ROLE_LABEL_NAME = 'default_role';

    public const FILTER_SEPARATOR_NAME = 'access_separator';

    public const NO_CHECK_LABEL_NAME = 'access_no_rights_check';

    public const DOCBLOCK_CHECK_LABEL_NAME = 'deny_fuzzy';

    public const DEFAULT_FILTER_NAME = 'default_access_filter';

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * AccessConfig constructor.
     *
     * @param array $config
     *
     * @throws ConfigException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function __construct(array $config)
    {
        if (empty($config)) {
            throw new ConfigException(translate('access.config-missing'));
        }

        if (empty($config[static::CACHE_STORAGE_SECTION_NAME])
            || empty($config[static::CACHE_STORAGE_SECTION_NAME]['type'])) {
            throw new ConfigException(translate('access.cache-config-missing'));
        }

        if (!isset($config[static::ROLES_SECTION_NAME])) {
            throw new ConfigException(translate('access.roles-missing'));
        }

        if (!\is_array($config[static::ROLES_SECTION_NAME])) {
            throw new ConfigException(translate('access.roles-list-incorrect'));
        }

        /*if (empty($config[static::FILTER_DIRECTIVE_NAME])) {
            throw new ConfigException(translate('access.filter-field-config-missing'));
        }

        if (empty($config[static::ANNOTATION_LABEL_NAME])) {
            throw new ConfigException(translate('access.access-check-config-lanel-missing'));
        }*/

        if (empty($config[static::FILTER_SEPARATOR_NAME])) {
            throw new ConfigException(translate('access.filter-delimiter-config-missing'));
        }

        if (empty($config[static::DEFAULT_ROLE_LABEL_NAME])) {
            throw new ConfigException(translate('access.default-role-config-missing'));
        }

        if (empty($config[static::DEFAULT_FILTER_NAME])) {
            throw new ConfigException(translate('access.default-filter-name-missing'));
        }

        $this->properties = $config;
    }

    /**
     * @param string $property
     *
     * @return mixed|null
     */
    public function get(string $property)
    {
        if (!\array_key_exists($property, $this->properties)) {
            return null;
        }

        return $this->properties[$property];
    }
}

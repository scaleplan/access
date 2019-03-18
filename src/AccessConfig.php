<?php

namespace Scaleplan\Access;

use Scaleplan\Access\Exceptions\ConfigException;

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

    public const PERSISTENT_STORAGE_SECTION_NAME = 'persistent_storage';

    public const ROLES_SECTION_NAME = 'roles';

    public const DEFAULT_ROLE_LABEL_NAME = 'default_role';

    public const FILTER_SEPARATOR_NAME = 'access_separator';

    public const NO_CHECK_LABEL_NAME = 'access_no_rights_check';

    public const DOCBLOCK_CHECK_LABEL_NAME = 'deny_fuzzy';

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
     */
    public function __construct(array $config)
    {
        if (empty($config)) {
            throw new ConfigException('Отсутствует конфигурация');
        }

        if (empty($config[static::PERSISTENT_STORAGE_SECTION_NAME])
            || empty($config[static::PERSISTENT_STORAGE_SECTION_NAME]['type'])
            || empty($config[static::PERSISTENT_STORAGE_SECTION_NAME]['user'])
            || empty($config[static::PERSISTENT_STORAGE_SECTION_NAME]['password'])
            || empty($config[static::PERSISTENT_STORAGE_SECTION_NAME]['dns'])) {
            throw new ConfigException(
                'В конфигурации отсутствуют данные о подключениие к постоянному хранилищу прав'
            );
        }

        if (empty($config[static::CACHE_STORAGE_SECTION_NAME])
            || empty($config[static::CACHE_STORAGE_SECTION_NAME]['type'])) {
            throw new ConfigException(
                'В конфигурации отсутствуют необходимые данные о подключениие к кэширующему хранилищу прав'
            );
        }

        if (!isset($config[static::ROLES_SECTION_NAME])) {
            throw new ConfigException('Отсутствует список ролей');
        }

        if (!\is_array($config[static::ROLES_SECTION_NAME])) {
            throw new ConfigException('Список ролей должен быть задан списком');
        }

        if (empty($config[static::FILTER_DIRECTIVE_NAME])) {
            throw new ConfigException('В конфигурации отсутствует имя для метки фильтрующего аргумента');
        }

        if (empty($config[static::ANNOTATION_LABEL_NAME])) {
            throw new ConfigException('В конфигурации отсутствует имя метки Access');
        }

        if (empty($config[static::FILTER_SEPARATOR_NAME])) {
            throw new ConfigException('В конфигурации отсутствует разделитель фильтров');
        }

        $this->properties = $config;
    }

    /**
     * @param string $property
     *
     * @return mixed|null
     */
    public function get(\string $property)
    {
        if (!\array_key_exists($property, $this->properties)) {
            return null;
        }

        return $this->properties[$property];
    }
}

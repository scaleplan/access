<?php

namespace Scaleplan\Access;

use Scaleplan\Access\Exceptions\AccessException;
use Scaleplan\Access\Exceptions\ConfigException;

/**
 * Класс внесения изменений
 *
 * Class AccessModify
 *
 * @package Scaleplan\Access
 */
class AccessModify extends AccessAbstract
{
    public const INIT_SQL_PATH = 'access.sql';

    /**
     * @var string
     */
    protected $role;

    public function __construct(int $userId, string $confPath)
    {
        parent::__construct($userId, $confPath);
        $this->role = $this->config->get(AccessConfig::DEFAULT_ROLE_LABEL_NAME);
    }

    /**
     * @param string $role
     *
     * @throws ConfigException
     */
    public function setRole(string $role) : void
    {
        if (!\in_array($role, $this->config->get(AccessConfig::ROLES_SECTION_NAME), true)) {
            throw new ConfigException('Заданная роль не входит в список доступных ролей');
        }

        $this->role = $role;
    }

    /**
     * @return array
     *
     * @throws ConfigException
     */
    public function getAccessRightsFromDb() : array
    {
        $sth = $this->getPSConnection()
            ->prepare('
                       SELECT 
                         u.text AS url,
                         COALESCE(ar.is_allow, true),
                         array_to_json(ar.ids) ids
                       FROM 
                         access.url u 
                       LEFT JOIN access.default_right dr
                         ON dr.url_id = u.id
                       LEFT JOIN access.access_right ar 
                         ON ar.url_id = u.id
                       WHERE 
                         ar.user_id = :user_id OR dr.role = :role
                    ');
        $sth->execute(
            ['user_id' => $this->userId, 'role' => $this->role]
        );

        return $sth->fetchAll();
    }

    /**
     * @param array|null $accessRights
     *
     * @throws ConfigException
     */
    public function saveAccessRightsToCache(array $accessRights = null) : void
    {
        $accessRights = $accessRights ?? $this->getAccessRightsFromDb();
        $this->cache->saveToCache($accessRights);
    }

    /**
     * Залить в базу данных схему для работы с Access
     *
     * @throws ConfigException
     */
    public function initSQLScheme() : void
    {
        $sql = file_get_contents(dirname(__DIR__) . static::INIT_SQL_PATH);
        $this->getPSConnection()->exec($sql);
    }

    /**
     * @throws ConfigException
     * @throws Exceptions\FormatException
     * @throws \ReflectionException
     */
    public function initPersistentScheme() : void
    {
        $sth = $this->getPSConnection()->prepare(
            'INSERT INTO
                                  access.url
                                 (text,
                                  name,
                                  model_type_id,
                                  type)
                                VALUES
                                 (:text,
                                  :name,
                                  :model_id,
                                  :type)
                                ON CONFLICT 
                                  (text) 
                                DO UPDATE SET 
                                  model_type_id = EXCLUDED.model_type_id,
                                  type = EXCLUDED.type,
                                  name = EXCLUDED.name'
        );
        /** @var Access $access */
        $access = Access::getInstance($this->userId);
        $urlGenerator = new AccessUrlGenerator($access);
        foreach ($urlGenerator->getAllURLs() as $arr) {
            $sth->execute($arr);
        }
    }

    /**
     * @throws ConfigException
     */
    public function initPersistentStorageTypes() : void
    {
        $roles = [];
        foreach ($this->config->get(AccessConfig::ROLES_SECTION_NAME) as $index => $role) {
            $roles["value{$index}"] = $role;
        }

        $rolesPlaceholders = implode(',', array_map(static function ($item) {
            return ":$item";
        }, array_keys($roles)));
        $sth = $this->getPSConnection()->prepare("CREATE TYPE access.roles AS ENUM ($rolesPlaceholders)");
        $sth->execute($roles);
    }

    /**
     * Инициальзировать персистентное хранилище данных о правах доступа
     *
     * @throws ConfigException
     * @throws Exceptions\FormatException
     * @throws \ReflectionException
     */
    public function initPersistentStorage() : void
    {
        $this->initSQLScheme();
        $this->initPersistentScheme();
        $this->initPersistentStorageTypes();
    }

    /**
     * обавить/изменить права доступа по умолчанию для роли
     *
     * @param int $urlId - идентификатор урла
     * @param string $role - наименование роли
     *
     * @return array
     *
     * @throws AccessException
     */
    public function addRoleAccessRight(int $urlId, string $role) : array
    {
        $sth = $this->getPSConnection()->prepare(
            'INSERT INTO
                                  access.default_right
                                VALUES
                                 (:url_id,
                                  :role)
                                ON CONFLICT 
                                  (url_id,
                                   role) 
                                DO NOTHING 
                                RETURNING
                                  *'
        );
        $sth->execute(['url_id' => $urlId, 'role' => $role,]);

        return $sth->fetchAll();
    }

    /**
     * Выдать роль пользователю
     *
     * @param int $userId - идентификатор пользователя
     * @param string $role - наименование роли
     *
     * @return array
     *
     * @throws AccessException
     */
    public function addUserToRole(int $userId, string $role) : array
    {
        $sth = $this->getPSConnection()->prepare(
            'INSERT INTO
                          access.user_role
                        VALUES
                         (:role,
                          :user_id)
                        ON CONFLICT ON CONSTRAINT
                          user_role_pkey
                        DO NOTHING
                        RETURNING
                          *');
        $sth->execute(['role' => $role, 'user_id' => $userId,]);

        return $sth->fetchAll();
    }

    /**
     * Добавить/изменить право дотупа
     *
     * @param int $urlId - идентификатор урла
     * @param int $userId - идентификатор пользователя
     * @param bool $isAllow - $values будут разрешающими или запрещающими
     * @param array $ids - с какими значения фильтра разрешать/запрещать доступ
     *
     * @return array
     *
     * @throws AccessException
     */
    public function addAccessRight(int $urlId, int $userId, bool $isAllow, array $ids) : array
    {
        $sth = $this->getPSConnection()->prepare(
            'INSERT INTO
                                  access.access_right
                                VALUES
                                 (:url_id,
                                  :user_id,
                                  :is_allow,
                                  :ids::varchar[])
                                ON CONFLICT 
                                  (url_id,
                                   user_id,
                                   is_allow,
                                   ids) 
                                DO UPDATE SET 
                                  is_allow = EXCLUDED.is_allow,
                                  ids = EXCLUDED.ids
                                RETURNING
                                  *');
        $sth->execute(
            [
                'url_id'   => $urlId,
                'user_id'  => $userId,
                'is_allow' => $isAllow,
                'ids'   => "{'" . implode("', '", $ids) . "'}::int8[]",
            ]
        );

        return $sth->fetchAll();
    }

    /**
     * Создать право доступа для пользователя на основе прав для его роли
     *
     * @param int $userId - идентификатор пользователя
     *
     * @return array
     *
     * @throws AccessException
     */
    public function shiftAccessRightFromRole(int $userId) : array
    {
        $sth = $this->getPSConnection()->prepare(
            'INSERT INTO
                                  access.access_right
                                 (url_id,
                                  user_id)
                                SELECT 
                                  dr.url_id,
                                  ur.user_id
                                FROM
                                  access.default_right dr 
                                JOIN 
                                  access.user_role ur 
                                  ON 
                                    ur.role = dr.role
                                WHERE
                                  ur.user_id = :user_id
                                ON CONFLICT 
                                  (url_id,
                                   user_id) 
                                DO UPDATE SET 
                                  is_allow = EXCLUDED.is_allow,
                                  ids = EXCLUDED.ids');

        $sth->execute(['user_id' => $userId]);

        return $sth->fetchAll();
    }
}

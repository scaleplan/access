<?php

namespace Scaleplan\Access;

use Scaleplan\Access\Exceptions\ConfigException;
use Scaleplan\Db\Interfaces\DbInterface;
use function Scaleplan\Translator\translate;

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

    /**
     * AccessModify constructor.
     *
     * @param DbInterface $psconnection
     * @param int $userId
     * @param string $confPath
     *
     * @throws ConfigException
     * @throws Exceptions\CacheTypeNotSupportingException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Helpers\Exceptions\EnvNotFoundException
     */
    public function __construct(DbInterface $psconnection, int $userId, string $confPath)
    {
        parent::__construct($psconnection, $userId, $confPath);
        $sth = $this->getPSConnection()->prepare('
                       SELECT 
                         role
                       FROM 
                         access.user_role ur
                       WHERE 
                         ur.user_id = :user_id
                    ');
        $sth->execute(['user_id' => $this->userId]);

        $this->setRole($sth->fetch()['role'] ?? $this->config->get(AccessConfig::DEFAULT_ROLE_LABEL_NAME));
    }

    /**
     * @param string $role
     *
     * @throws ConfigException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function setRole(string $role) : void
    {
        if (!\in_array($role, $this->config->get(AccessConfig::ROLES_SECTION_NAME), true)) {
            throw new ConfigException(translate('access.role-does-not-exist'));
        }

        $this->role = $role;
    }

    /**
     * @return array
     */
    public function getAccessRightsFromDb() : array
    {
        $sth = $this->getPSConnection()
            ->prepare('
                       SELECT 
                         u.text AS url,
                         COALESCE(rr.url_id, ur.url_id) url_id,     
                         COALESCE(ur.is_allow, rr.is_allow) is_allow,
                         array_to_json(COALESCE(ur.ids, rr.ids)) ids
                       FROM 
                         access.url u 
                       LEFT JOIN access.role_right rr
                         ON rr.url_id = u.id
                       LEFT JOIN access.user_right ur 
                         ON ur.url_id = u.id
                       WHERE 
                         ur.user_id = :user_id OR rr.role = :role
                    ');
        $sth->execute(
            ['user_id' => $this->userId, 'role' => $this->role]
        );

        return $sth->fetchAll();
    }

    /**
     * @param array|null $accessRights
     */
    public function saveAccessRightsToCache(array $accessRights = null) : void
    {
        $accessRights = $accessRights ?? $this->getAccessRightsFromDb();
        $this->cache->saveToCache($accessRights);
    }

    /**
     * Залить в базу данных схему для работы с правами доступа
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
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Helpers\Exceptions\EnvNotFoundException
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
                                  model_type_id = excluded.model_type_id,
                                  type = excluded.type,
                                  name = excluded.name'
        );
        /** @var Access $access */
        $access = Access::getInstance($this->storage, $this->userId, $this->confPath);
        $urlGenerator = new AccessUrlGenerator($access);
        foreach ($urlGenerator->getAllURLs() as $arr) {
            $sth->execute($arr);
        }
    }

    /**
     * Инициализация ролей в базе
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
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Helpers\Exceptions\EnvNotFoundException
     */
    public function initPersistentStorage() : void
    {
        $this->initSQLScheme();
        $this->initPersistentScheme();
        $this->initPersistentStorageTypes();
    }

    /**
     * Добавить/изменить права доступа по умолчанию для роли
     *
     * @param int $urlId - идентификатор урла
     * @param string $role - наименование роли
     *
     * @return array
     */
    public function addRoleRight(int $urlId, string $role) : array
    {
        $sth = $this->getPSConnection()->prepare(
            'INSERT INTO
                                  access.role_right
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
     */
    public function addUserRight(int $urlId, int $userId, bool $isAllow, array $ids) : array
    {
        $sth = $this->getPSConnection()->prepare(
            'INSERT INTO access.user_right(url_id, user_id, is_allow, ids)
             VALUES (:url_id, :user_id, :is_allow, :ids::int4[])
             ON CONFLICT
             DO UPDATE SET 
               is_allow = EXCLUDED.is_allow, 
               ids = EXCLUDED.ids
             RETURNING *'
        );
        $sth->execute(
            [
                'url_id'   => $urlId,
                'user_id'  => $userId,
                'is_allow' => $isAllow,
                'ids'      => "{'" . implode("', '", $ids) . "'}",
            ]
        );

        return $sth->fetchAll();
    }
}

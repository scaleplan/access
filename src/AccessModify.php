<?php

namespace Scaleplan\Access;

use Scaleplan\Access\Constants\ConfigConstants;
use Scaleplan\Access\Constants\SessionConstants;
use Scaleplan\Access\Exceptions\AccessException;
use Scaleplan\Access\Exceptions\ConfigException;
use Scaleplan\Redis\RedisSingleton;

/**
 * Класс внесения изменений
 *
 * Class AccessModify
 *
 * @package Scaleplan\Access
 */
class AccessModify extends AccessAbstract
{
    /**
     * Инстанс класса
     *
     * @var null|AccessModify
     */
    protected static $instance;

    /**
     * @throws AccessException
     * @throws ConfigException
     * @throws \Scaleplan\Redis\Exceptions\RedisSingletonException
     */
    public function loadAccessRights(): void
    {
        $sth = $this->getPSConnection()
            ->prepare('
                       SELECT 
                         u.text AS url,
                         ar.is_allow,
                         array_to_json(ar.values) "values"
                       FROM 
                         access.url u 
                       LEFT JOIN
                         access.access_right ar 
                         ON 
                           ar.url_id = u.id
                       WHERE 
                         ar.user_id = :user_id
                    ');
        $sth->execute(['user_id' => $this->userId]);

        $accessRights = $sth->fetchAll();

        $cacheStorageName = $this->config[ConfigConstants::CACHE_STORAGE_SECTION_NAME];
        switch ($cacheStorageName) {
            case 'redis':
                if (empty($this->config[$cacheStorageName]['socket'])) {
                    throw new ConfigException('В конфигурации не задан путь к Redis-сокету');
                }

                $this->cs = $this->cs ?? RedisSingleton::create($this->config[$cacheStorageName]['socket']);
                $this->cs->delete("user_id:{$this->userId}");

                if ($accessRights) {
                    $hashValue = array_map(function ($item) {
                        return json_encode(
                            $item,
                            JSON_FORCE_OBJECT) ?? $item;
                    }, array_column($accessRights, null, 'url'));

                    if (!$this->cs->hMset("user_id:{$this->userId}", $hashValue))
                    {
                        throw new AccessException('Не удалось записать права доступа в Redis');
                    }
                }

                break;

            case 'session':
                $_SESSION[SessionConstants::SESSION_ACCESS_RIGHTS_SECTION_NAME]
                    = array_column($accessRights, null, 'url');
                break;

            default:
                throw new ConfigException(
                    "Драйвер $cacheStorageName кэширующего хранилища не поддерживается системой"
                );
        }
    }

    /**
     * Залить в базу данных схему для работы с Access
     *
     * @return int
     *
     * @throws AccessException
     */
    protected function initSQLScheme(): int
    {
        $sql = file_get_contents(__DIR__ . '/access.sql');

        return $this->getPSConnection()->exec($sql);
    }

    /**
     * Инициальзировать персистентное хранилище данных о правах доступа
     *
     * @return int
     *
     * @throws AccessException
     * @throws ConfigException
     * @throws \ReflectionException
     */
    public function initPersistentStorage(): int
    {
        if (!$this->initSQLScheme()) {
            throw new AccessException('Не удалось создать необходимые объекты базы данных');
        }

        $urlsCount = 0;

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
        $access = Access::create($this->userId);
        foreach ($access->getAllURLs() as $arr) {
            $sth->execute($arr);
            $urlsCount += $sth->rowCount();
        }

        $roles = [];
        foreach ($this->config[ConfigConstants::ROLES_SECTION_NAME] as $index => $role) {
            $roles["value{$index}"] = $role;
        }

        $rolesPlaceholders = implode(',', array_map( function ($item) {
            return ":$item";
        }, array_keys($roles)));
        $sth = $this->ps->prepare("CREATE TYPE access.roles AS ENUM ($rolesPlaceholders)");
        $sth->execute($roles);

        return $urlsCount;
    }

    /**
     * обавить/изменить права доступа по умолчанию для роли
     *
     * @param int $url_id - идентификатор урла
     * @param string $role - наименование роли
     *
     * @return array
     *
     * @throws AccessException
     */
    public function addRoleAccessRight(int $url_id, string $role): array
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
        $sth->execute(
            [
                'url_id'   => $url_id,
                'role'     => $role
            ]
        );

        return $sth->fetchAll();
    }

    /**
     * Выдать роль пользователю
     *
     * @param int $user_id - идентификатор пользователя
     * @param string|null $role - наименование роли
     *
     * @return array
     *
     * @throws AccessException
     */
    public function addUserToRole(int $user_id, string $role = ''): array
    {
        $role = $role ?? $this->config[ConfigConstants::DEFAULT_ROLE_LABEL_NAME];
        if (!$role) {
            throw new ConfigException('Не задана роль по умолчанию');
        }

        if (!\in_array($role, $this->config[ConfigConstants::ROLES_SECTION_NAME], true)) {
            throw new ConfigException('Заданная роль не входит в список доступных ролей');
        }

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
        $sth->execute(
            [
                'role'    => $role,
                'user_id' => $user_id
            ]
        );

        return $sth->fetchAll();
    }

    /**
     * Добавить/изменить право дотупа
     *
     * @param int $url_id - идентификатор урла
     * @param int $user_id - идентификатор пользователя
     * @param bool $is_allow - $values будут разрешающими или запрещающими
     * @param array $values - с какими значения фильтра разрешать/запрещать доступ
     *
     * @return array
     *
     * @throws AccessException
     */
    public function addAccessRight(int $url_id, int $user_id, bool $is_allow, array $values): array
    {
        $sth = $this->getPSConnection()->prepare(
                     'INSERT INTO
                                  access.access_right
                                VALUES
                                 (:url_id,
                                  :user_id,
                                  :is_allow,
                                  :values::varchar[])
                                ON CONFLICT 
                                  (url_id,
                                   user_id,
                                   is_allow,
                                   values) 
                                DO UPDATE SET 
                                  is_allow = EXCLUDED.is_allow,
                                  values = EXCLUDED.values
                                RETURNING
                                  *');
        $sth->execute(
            [
                'url_id'   => $url_id,
                'user_id'     => $user_id,
                'is_allow' => $is_allow,
                'values'   => "{'" . implode("', '", $values) . "'}"
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
    public function shiftAccessRightFromRole(int $userId): array
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
                                  values = EXCLUDED.values');

        $sth->execute(['user_id' => $userId]);

        return $sth->fetchAll();
    }
}
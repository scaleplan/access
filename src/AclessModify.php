<?php

namespace avtomon;

/**
 * Класс внесения изменений
 *
 * Class AclessModify
 * @package avtomon
 */
class AclessModify extends AclessAbstract
{
    protected static $instance = null;

    /**
     *
     * Загрузить права доступа для текущего пользователя в кэш
     *
     * @throws AclessException
     */
    public function loadAclessRights(): void
    {
        $sth = $this->getPSConnection()
            ->prepare('SELECT 
                         u.text AS url,
                         ar.is_allow,
                         array_to_json(ar.values) "values"
                       FROM 
                         acless.url u 
                       LEFT JOIN
                         acless.access_right ar 
                         ON 
                           ar.url_id = u.id
                       WHERE 
                         ar.user_id = :user_id');
        $sth->execute(['user_id' => $this->userId]);

        $accessRights = $sth->fetchAll();

        switch ($this->config['cache_storage']) {
            case 'redis':
                if (empty($this->config['redis']['socket'])) {
                    throw new AclessException('В конфигурации не задан путь к Redis-сокету', 34);
                }

                $this->cs = $this->cs ?? RedisSingleton::create($this->config['redis']['socket']);
                $this->cs->delete("user_id:{$this->userId}");

                if ($accessRights) {
                    $hashValue = array_map(function ($item) {
                        return json_encode(
                            $item,
                            JSON_FORCE_OBJECT) ?? $item;
                    }, array_column($accessRights, null, 'url'));

                    if (!$this->cs->hMSet("user_id:{$this->userId}", $hashValue))
                    {
                        throw new AclessException('Не удалось записать права доступа в Redis', 35);
                    }
                }

                break;

            case 'session':
                $_SESSION['access_rights'] = array_column($accessRights, null, 'url');
                break;

            default:
                throw new AclessException("Драйвер {$this->config['cache_storage']} кэширующего хранилища не поддерживается системой", 36);
        }
    }

    /**
     * Залить в базу данных схема для работы с Acless
     *
     * @return int
     */
    protected function initSQLScheme(): int
    {
        $sql = file_get_contents(__DIR__ . '/acless.sql');

        return $sth = $this->getPSConnection()->exec($sql);
    }

    /**
     * Инициальзировать персистентное хранилище данных о правах доступа
     *
     * @return int
     */
    public function initPersistentStorage(): int
    {
        if (!$this->initSQLScheme()) {
            throw new AclessException('Не удалось создать необходимые объекты базы данных', 37);
        }

        $urlsCount = $defaultRightsCount = 0;

        $sth = $this->getPSConnection()->prepare(
            'INSERT INTO
                                  acless.url
                                 (text,
                                  name,
                                  model_id,
                                  type)
                                VALUES
                                 (:text,
                                  :name,
                                  :model_id,
                                  :type)
                                ON CONFLICT 
                                  (text) 
                                DO UPDATE SET 
                                  model_id = EXCLUDED.model_id,
                                  type = EXCLUDED.type,
                                  name = EXCLUDED.name'
        );
        foreach ($this->getAllURLs() as $arr) {
            $sth->execute($arr);
            $urlsCount += $sth->rowCount();
        }

        if (empty($this->config['roles'])) {
            return [];
        }

        $roles = [];
        foreach ($acless->getConfig()['roles'] as $index => $role) {
            $roles["value{$index}"] = $role;
        }

        $rolesPlaceholders = implode(',', array_map( function ($item) {
            return ":$item";
        }, array_keys($roles)));
        $sth = $this->ps->prepare("CREATE TYPE acless.roles AS ENUM ($rolesPlaceholders)");
        $sth->execute($roles);

        return $urlsCount;
    }

    /**
     * Добавить/изменить права доступа для роли
     *
     * @param int $url_id - идентификатор урла
     * @param string $role - наименование роли
     * @param bool $is_allow - $values будут разрешающими или запрещающими
     * @param array $values - с какими значения фильтра разрешать/запрещать доступ
     *
     * @return array
     */
    public function addRoleAccessRight(int $url_id, string $role): array
    {
        $sth = $this->getPSConnection()->prepare(
            'INSERT INTO
                                  acless.default_right
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
     * @throws AclessException
     */
    public function addUserToRole(int $user_id, string $role = null): array
    {
        $role = $role ?? ($this->config['default_role'] || null);
        if (!$role) {
            throw new AclessException('Не задана роль по умолчанию', 38);
        }

        if (empty($this->config['roles'])) {
            throw new AclessException('Список ролей пуст', 39);
        }

        if (!in_array($role, $this->config['roles'])) {
            throw new AclessException('Заданная роль не входит в список доступных ролей', 40);
        }

        $sth = $this->getPSConnection()->prepare(
            'INSERT INTO
                          acless.default_right
                        VALUES
                         (:url_id,
                          :role)
                        ON CONFLICT 
                         (url_id,
                          role) 
                        DO NOTHING
                        RETURNING
                          *');
        $sth->execute(
            [
                'url_id'   => $url_id,
                'role'     => $role
            ]
        );

        return $sth->fetchAll();
    }

    /**
     * Добавить/изменить право дотупа
     *
     * @param int $url_id - идентификатор урла
     * @param int $user_id - идентификатор пользователя
     *
     * @return array
     */
    public function addAccessRight(int $url_id, int $user_id, bool $is_allow, array $values): array
    {
        $sth = $this->getPSConnection()->prepare(
            'INSERT INTO
                                  acless.access_right
                                VALUES
                                 (:url_id,
                                  :user_id,
                                  :is_allow,
                                  :values::int[])
                                ON CONFLICT 
                                  (url_id,
                                   user_id) 
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
                'values'   => '{' . implode(',', $values) . '}'
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
     */
    public function shiftAccessRightFromRole(int $userId)
    {
        $sth = $this->getPSConnection()->prepare(
            'INSERT INTO
                                  acless.access_right
                                SELECT 
                                  dr.url_id,
                                  ur.user_id,
                                  dr.is_allow,
                                  dr.values
                                FROM
                                  acless.default_right dr 
                                JOIN 
                                  acless.user_role ur 
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
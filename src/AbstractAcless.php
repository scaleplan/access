<?php

namespace avtomon;

use phpDocumentor\Reflection\DocBlockFactory;
use Symfony\Component\Yaml\Yaml;

/**
 * Суперкласс
 *
 * Class AbstractAcless
 * @package avtomon
 */
abstract class AbstractAcless
{
    protected $config = null; // Конфигурация
    protected $userId = 0; // Идентификатор пользователя

    protected $ps = null; // Подключение к РСУБД
    protected $cs = null; // Подключение к кэшу

    protected static $instance = null;

    /**
     * @param int|null $userId - Идентификатор пользователя
     *
     * @return Acless
     *
     * @throws AclessException
     */
    public static function create(int $userId = null)
    {
        if (!self::$instance) {
            if (!$userId) {
                throw new AclessException('Необходимо передать идентификатор пользователя в качестве параметра');
            }

            self::$instance = new Acless($userId);
        }

        return self::$instance;
    }

    protected function __construct(int $userId)
    {
        $this->config = Yaml::parse(file_get_contents(__DIR__ . '/config.yml'));

        if (empty($this->config)) {
            throw new AclessException('Отсутствует конфигурация');
        }

        if (empty($this->config['persistent_storage'])) {
            throw new AclessException('В конфирурациии отсутствует указание постоянного хранилища');
        }

        if (empty($this->config[$this->config['persistent_storage']])){
            throw new AclessException('В конфигурации отсутствуют данные о подключениие к постоянному хранилищу прав');
        }

        if (empty($this->config['cache_storage'])) {
            throw new AclessException('В конфирурациии отсутствует указание кефирующего хранилища');
        }

        if (empty($this->config[$this->config['cache_storage']])){
            throw new AclessException('В конфигурации отсутствуют данные о подключениие к кэширующему хранилищу прав');
        }

        if (!is_array($this->config['roles'])){
            throw new AclessException('Список ролей должен быть задан списком');
        }

        if ($userId < 1) {
            throw new AclessException('Неверное задан идентификатор пользователя');
        }

        $this->userId = $userId;
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    /**
     * Вернуть подключение в РСУБД
     *
     * @return \PDO
     *
     * @throws AclessException
     */
    protected function getPSConnection(): \PDO
    {
        if ($this->ps) {
            return $this->ps;
        }

        switch ($this->config['persistent_storage']) {
            case 'postgresql':
                if (empty($this->config['postgresql']) || empty($this->config['postgresql']['dns']) || empty($this->config['postgresql']['user']) || empty($this->config['postgresql']['password'])) {
                    throw new AclessException('Недостаточно данных для подключения к PostgreSQL');
                }

                return $this->ps = $this->ps ?? new \PDO(
                        $this->config['postgresql']['dns'],
                        $this->config['postgresql']['user'],
                        $this->config['postgresql']['password']
                    );
                break;

            default:
                throw new AclessException("Драйвер {$this->config['persistent_storage']} постоянного хранилища не поддерживается системой");
        }
    }

    /**
     * Вернуть конфигурацию
     *
     * @return array
     */
    protected function getConfig(): array
    {
        return $this->config;
    }
}
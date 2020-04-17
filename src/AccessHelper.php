<?php
declare(strict_types=1);

namespace Scaleplan\Access;

/**
 * Класс вспомогательных методов
 *
 * Class AccessHelper
 *
 * @package Scaleplan\Access
 */
class AccessHelper
{
    /**
     * Превратить строку в виде camelCase в строку вида dashed (camelCase -> camel-case)
     *
     * @param string $str - строка в camelCase
     *
     * @return string
     */
    public static function camel2dashed(string $str): string
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $str));
    }
}

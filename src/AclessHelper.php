<?php

namespace avtomon;

/**
 * Класс ошибок
 *
 * Class AccessHelperException
 * @package avtomon
 */
class AccessHelperException extends CustomException
{
}

/**
 * Класс вспомогательных методов
 *
 * Class AccessHelper
 * @package avtomon
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
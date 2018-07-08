<small>avtomon</small>

AclessHelper
============

Класс хэлперов

Описание
-----------

Class AclessHelper

Сигнатура
---------

- **class**.

Методы
-------

Методы класса class:

- [`sanitizeMethodArgs()`](#sanitizeMethodArgs) &mdash; Проверить аргументы метода
- [`sanitizeSQLPropertyArgs()`](#sanitizeSQLPropertyArgs) &mdash; Проверить аргументы для свойства-метода
- [`getSQLParams()`](#getSQLParams) &mdash; Получить из SQL-запроса все параметры
- [`camel2dashed()`](#camel2dashed) &mdash; Превратить строку в виде camelCase в строку вида dashed (camelCase -&gt; camel-case)

### `sanitizeMethodArgs()` <a name="sanitizeMethodArgs"></a>

Проверить аргументы метода

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$method` ([`ReflectionMethod`](http://php.net/class.ReflectionMethod)) &mdash; - Reflection-обертка для метода
    - `$args` (`array`) &mdash; - массив аргументов
- Возвращает `array` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `sanitizeSQLPropertyArgs()` <a name="sanitizeSQLPropertyArgs"></a>

Проверить аргументы для свойства-метода

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$property` ([`ReflectionProperty`](http://php.net/class.ReflectionProperty)) &mdash; - Reflection-обертка для SQL-свойства
    - `$args` (`array`) &mdash; - массив аргументов
    - `$object` (`object`) &mdash; - объект модели
- Возвращает `array` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `getSQLParams()` <a name="getSQLParams"></a>

Получить из SQL-запроса все параметры

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$sql`
- Возвращает `array` value.

### `camel2dashed()` <a name="camel2dashed"></a>

Превратить строку в виде camelCase в строку вида dashed (camelCase -> camel-case)

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$str` (`string`) &mdash; - строка в camelCase
- Возвращает `string` value.


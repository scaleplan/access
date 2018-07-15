<small>avtomon</small>

AclessSanitize
==============

Класс проверки аргументов выполнения

Описание
-----------

Class AclessSanitize

Сигнатура
---------

- **class**.

Свойства
----------

class устанавливает следующие свойства:

- [`$reflector`](#$reflector) &mdash; Отражение метода или SQL-свойства
- [`$args`](#$args) &mdash; Массив аргументов
- [`$sanitizedArgs`](#$sanitizedArgs) &mdash; Массив очещенных аргументов

### `$reflector` <a name="reflector"></a>

Отражение метода или SQL-свойства

#### Сигнатура

- **protected** property.
- Может быть одного из следующих типов:
    - [`ReflectionMethod`](http://php.net/class.ReflectionMethod)
    - [`ReflectionProperty`](http://php.net/class.ReflectionProperty)

### `$args` <a name="args"></a>

Массив аргументов

#### Сигнатура

- **protected** property.
- Значение `array`.

### `$sanitizedArgs` <a name="sanitizedArgs"></a>

Массив очещенных аргументов

#### Сигнатура

- **protected** property.
- Может быть одного из следующих типов:
    - `array`
    - `null`

Методы
-------

Методы класса class:

- [`__construct()`](#__construct) &mdash; Конструктор
- [`sanitizeArgs()`](#sanitizeArgs) &mdash; Проверить и очистить аргументы
- [`sanitizeMethodArgs()`](#sanitizeMethodArgs) &mdash; Проверить аргументы метода
- [`sanitizeSQLPropertyArgs()`](#sanitizeSQLPropertyArgs) &mdash; Проверить аргументы для свойства-метода
- [`argAvailabilityCheck()`](#argAvailabilityCheck) &mdash; Проверка наличия аргументов
- [`getDocParams()`](#getDocParams) &mdash; Вернуть массив DOCBLOCK-параметров и подгруппу необязательных параметров
- [`docTypeCheck()`](#docTypeCheck) &mdash; Проверка аргументов на соответствие типу
- [`typeCheck()`](#typeCheck) &mdash; Проверка значения на соответствие типу
- [`getSQLParams()`](#getSQLParams) &mdash; Получить из SQL-запроса все параметры

### `__construct()` <a name="__construct"></a>

Конструктор

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$reflector` ([`Reflector`](http://php.net/class.Reflector)) &mdash; - отражение метода или SQL-свойства
    - `$args` (`array`) &mdash; - массив аргументов
- Ничего не возвращает.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `sanitizeArgs()` <a name="sanitizeArgs"></a>

Проверить и очистить аргументы

#### Сигнатура

- **public** method.
- Возвращает `array` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

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
- Возвращает `array` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `argAvailabilityCheck()` <a name="argAvailabilityCheck"></a>

Проверка наличия аргументов

#### Сигнатура

- **protected static** method.
- Может принимать следующий параметр(ы):
    - `$paramName` (`string`) &mdash; - имя параметра
    - `$args` (`array`) &mdash; - массив аргументов
    - `$optionParams` (`array`) &mdash; - массив опциональных параметров
- Ничего не возвращает.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `getDocParams()` <a name="getDocParams"></a>

Вернуть массив DOCBLOCK-параметров и подгруппу необязательных параметров

#### Сигнатура

- **protected static** method.
- Может принимать следующий параметр(ы):
    - `$docParams` (`array`) &mdash; - исходный массив параметров
- Возвращает `array` value.

### `docTypeCheck()` <a name="docTypeCheck"></a>

Проверка аргументов на соответствие типу

#### Сигнатура

- **protected static** method.
- Может принимать следующий параметр(ы):
    - `$arg` &mdash; - значение аргумента
    - `$paramName` (`string`) &mdash; - имя аргумента/параметра
    - `$paramType` (`string`) &mdash; - требуемый тип или группа типов
    - `$docBlock` (`phpDocumentor\Reflection\DocBlock`) &mdash; - ссылка объект DOCBLOCK метода или свойства
- Ничего не возвращает.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `typeCheck()` <a name="typeCheck"></a>

Проверка значения на соответствие типу

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$value` &mdash; - значение
    - `$types` (`array`) &mdash; - принимаемые типы
    - `$denyFuzzy` (`bool`) &mdash; - строгое ли сравнение используется
- Возвращает `bool` value.

### `getSQLParams()` <a name="getSQLParams"></a>

Получить из SQL-запроса все параметры

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$sql`
- Возвращает `array` value.


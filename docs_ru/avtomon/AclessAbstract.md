<small>avtomon</small>

AclessAbstract
==============

Суперкласс

Описание
-----------

Class AbstractAcless

Сигнатура
---------

- **abstract class**.

Свойства
----------

abstract class устанавливает следующие свойства:

- [`$config`](#$config) &mdash; Конфигурация
- [`$userId`](#$userId) &mdash; Идентификатор пользователя
- [`$ps`](#$ps) &mdash; Подключение к РСУБД
- [`$cs`](#$cs) &mdash; Подключение к кэшу
- [`$instance`](#$instance) &mdash; Инстанс класса

### `$config` <a name="config"></a>

Конфигурация

#### Сигнатура

- **protected** property.
- Значение `array`.

### `$userId` <a name="userId"></a>

Идентификатор пользователя

#### Сигнатура

- **protected** property.
- Значение `int`.

### `$ps` <a name="ps"></a>

Подключение к РСУБД

#### Сигнатура

- **protected** property.
- Может быть одного из следующих типов:
    - `null`
    - [`PDO`](http://php.net/class.PDO)

### `$cs` <a name="cs"></a>

Подключение к кэшу

#### Сигнатура

- **protected** property.
- Может быть одного из следующих типов:
    - `null`
    - `Redis`

### `$instance` <a name="instance"></a>

Инстанс класса

#### Сигнатура

- **protected static** property.
- Может быть одного из следующих типов:
    - `null`
    - [`AclessAbstract`](../avtomon/AclessAbstract.md)

Методы
-------

Методы класса abstract class:

- [`create()`](#create) &mdash; Синглтон
- [`__construct()`](#__construct) &mdash; AclessAbstract constructor
- [`getUserId()`](#getUserId) &mdash; Возвращает идентификатор пользователя
- [`getPSConnection()`](#getPSConnection) &mdash; Вернуть подключение в РСУБД
- [`getConfig()`](#getConfig) &mdash; Вернуть конфигурацию или ее часть

### `create()` <a name="create"></a>

Синглтон

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$userId` (`int`) - идентификатор пользователя
    - `$confPath` (`string`) - путь в файлу конфигурации
- Возвращает [`AclessAbstract`](../avtomon/AclessAbstract.md) value.

### `__construct()` <a name="__construct"></a>

AclessAbstract constructor

#### Сигнатура

- **private** method.
- Может принимать следующий параметр(ы):
    - `$userId` (`int`) - идентификатор пользователя
    - `$confPath` (`string`) - пусть к конфигурации
- Ничего не возвращает.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `getUserId()` <a name="getUserId"></a>

Возвращает идентификатор пользователя

#### Сигнатура

- **public** method.
- Возвращает `int` value.

### `getPSConnection()` <a name="getPSConnection"></a>

Вернуть подключение в РСУБД

#### Сигнатура

- **protected** method.
- Возвращает [`PDO`](http://php.net/class.PDO) value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `getConfig()` <a name="getConfig"></a>

Вернуть конфигурацию или ее часть

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$key` (`string`) - ключ конфигурации
- Может возвращать одно из следующих значений:
    - `array`
    - `mixed`
    - `null`


<small>avtomon</small>

AclessModify
============

Класс внесения изменений

Описание
-----------

Class AclessModify

Сигнатура
---------

- **class**.
- Является подклассом класса [`AclessAbstract`](../avtomon/AclessAbstract.md).

Свойства
----------

class устанавливает следующие свойства:

- [`$instance`](#$instance) &mdash; Инстанс класса

### `$instance` <a name="instance"></a>

Инстанс класса

#### Сигнатура

- **protected static** property.
- Может быть одного из следующих типов:
    - `null`
    - [`AclessModify`](../avtomon/AclessModify.md)

Методы
-------

Методы класса class:

- [`loadAclessRights()`](#loadAclessRights) &mdash; Загрузить права доступа для текущего пользователя в кэш
- [`initSQLScheme()`](#initSQLScheme) &mdash; Залить в базу данных схему для работы с Acless
- [`initPersistentStorage()`](#initPersistentStorage) &mdash; Инициальзировать персистентное хранилище данных о правах доступа
- [`addRoleAccessRight()`](#addRoleAccessRight) &mdash; обавить/изменить права доступа по умолчанию для роли
- [`addUserToRole()`](#addUserToRole) &mdash; Выдать роль пользователю
- [`addAccessRight()`](#addAccessRight) &mdash; Добавить/изменить право дотупа
- [`shiftAccessRightFromRole()`](#shiftAccessRightFromRole) &mdash; Создать право доступа для пользователя на основе прав для его роли

### `loadAclessRights()` <a name="loadAclessRights"></a>

Загрузить права доступа для текущего пользователя в кэш

#### Сигнатура

- **public** method.
- Ничего не возвращает.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - `avtomon\RedisSingletonException`

### `initSQLScheme()` <a name="initSQLScheme"></a>

Залить в базу данных схему для работы с Acless

#### Сигнатура

- **protected** method.
- Возвращает `int` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `initPersistentStorage()` <a name="initPersistentStorage"></a>

Инициальзировать персистентное хранилище данных о правах доступа

#### Сигнатура

- **public** method.
- Возвращает `int` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `addRoleAccessRight()` <a name="addRoleAccessRight"></a>

обавить/изменить права доступа по умолчанию для роли

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$url_id` (`int`) &mdash; - идентификатор урла
    - `$role` (`string`) &mdash; - наименование роли
- Возвращает `array` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `addUserToRole()` <a name="addUserToRole"></a>

Выдать роль пользователю

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$user_id` (`int`) &mdash; - идентификатор пользователя
    - `$role` (`string`) &mdash; - наименование роли
- Возвращает `array` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `addAccessRight()` <a name="addAccessRight"></a>

Добавить/изменить право дотупа

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$url_id` (`int`) &mdash; - идентификатор урла
    - `$user_id` (`int`) &mdash; - идентификатор пользователя
    - `$is_allow` (`bool`) &mdash; - $values будут разрешающими или запрещающими
    - `$values` (`array`) &mdash; - с какими значения фильтра разрешать/запрещать доступ
- Возвращает `array` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `shiftAccessRightFromRole()` <a name="shiftAccessRightFromRole"></a>

Создать право доступа для пользователя на основе прав для его роли

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$userId` (`int`) &mdash; - идентификатор пользователя
- Возвращает `array` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)


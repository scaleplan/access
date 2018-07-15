<small>avtomon</small>

Acless
======

Класс формирования списка урлов и проверки прав

Описание
-----------

Class Acless

Сигнатура
---------

- **class**.
- Является подклассом класса [`AclessAbstract`](../avtomon/AclessAbstract.md).

Константы
---------

class устанавливает следующие константы:

- [`ACLESS_403_ERROR_CODE`](#ACLESS_403_ERROR_CODE) &mdash; Код ошибки Acless указывающий на закрытый доступ к ресурсу
- [`ACLESS_UNAUTH_ERROR_CODE`](#ACLESS_UNAUTH_ERROR_CODE) &mdash; Код ошибки Acless указывающий на неразрешенный неавторизованный запрос

Свойства
----------

class устанавливает следующие свойства:

- [`$instance`](#$instance) &mdash; Инстанс класса
- [`$filterSeparator`](#$filterSeparator) &mdash; Разделитель значений фильтров

### `$instance` <a name="instance"></a>

Инстанс класса

#### Сигнатура

- **protected static** property.
- Может быть одного из следующих типов:
    - `null`
    - [`Acless`](../avtomon/Acless.md)

### `$filterSeparator` <a name="filterSeparator"></a>

Разделитель значений фильтров

#### Сигнатура

- **protected** property.
- Значение `string`.

Методы
-------

Методы класса class:

- [`getAccessRights()`](#getAccessRights) &mdash; Вернуть информацию о всех доступных пользователю урлах или о каком-то конкретном урле
- [`checkMethodRights()`](#checkMethodRights) &mdash; Проверить доступ к методу
- [`checkFileRights()`](#checkFileRights) &mdash; Проверить доступ к файлу
- [`methodToURL()`](#methodToURL) &mdash; Сформировать урл из имени контроллера и имени метода
- [`generateControllerURLs()`](#generateControllerURLs) &mdash; Сгенерировать массив урлов контроллеров
- [`getRecursivePaths()`](#getRecursivePaths) &mdash; Найти все файлы в каталоге, включая вложенные директории
- [`getControllerURLs()`](#getControllerURLs) &mdash; Возвращает все урлы контроллеров
- [`getFilesURLs()`](#getFilesURLs) &mdash; Возвращает все урлы файлов
- [`getPlainURLs()`](#getPlainURLs) &mdash; Возвращает урлы, непосредственно указанные в конфигурационном файле
- [`getAllURLs()`](#getAllURLs) &mdash; Возращает все собранные урлы

### `getAccessRights()` <a name="getAccessRights"></a>

Вернуть информацию о всех доступных пользователю урлах или о каком-то конкретном урле

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$url` (`string`) &mdash; - текст урла
- Возвращает `array` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - `avtomon\RedisSingletonException`

### `checkMethodRights()` <a name="checkMethodRights"></a>

Проверить доступ к методу

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$refMethod` ([`Reflector`](http://php.net/class.Reflector)) &mdash; - Reflection-обертка для метода
    - `$args` (`array`) &mdash; - параметры выполнения
    - `$refClass` ([`ReflectionClass`](http://php.net/class.ReflectionClass)) &mdash; - класс метода
- Возвращает `bool` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - `avtomon\RedisSingletonException`

### `checkFileRights()` <a name="checkFileRights"></a>

Проверить доступ к файлу

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$filePath` (`string`) &mdash; - путь к файлу
- Возвращает `bool` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - `avtomon\RedisSingletonException`

### `methodToURL()` <a name="methodToURL"></a>

Сформировать урл из имени контроллера и имени метода

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$className` (`string`) &mdash; - имя класса
    - `$methodName` (`string`) &mdash; - имя метода
- Возвращает `string` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `generateControllerURLs()` <a name="generateControllerURLs"></a>

Сгенерировать массив урлов контроллеров

#### Сигнатура

- **protected** method.
- Может принимать следующий параметр(ы):
    - `$controllerFileName` (`string`) &mdash; - имя файла контроллера
    - `$controllerNamespace` (`string`) &mdash; - пространство имен для конроллера, если есть
- Возвращает `array` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - [`ReflectionException`](http://php.net/class.ReflectionException)

### `getRecursivePaths()` <a name="getRecursivePaths"></a>

Найти все файлы в каталоге, включая вложенные директории

#### Сигнатура

- **protected** method.
- Может принимать следующий параметр(ы):
    - `$dir` (`string`) &mdash; - путь к каталогу
- Возвращает `array` value.

### `getControllerURLs()` <a name="getControllerURLs"></a>

Возвращает все урлы контроллеров

#### Сигнатура

- **public** method.
- Возвращает `array` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - [`ReflectionException`](http://php.net/class.ReflectionException)

### `getFilesURLs()` <a name="getFilesURLs"></a>

Возвращает все урлы файлов

#### Сигнатура

- **public** method.
- Возвращает `array` value.

### `getPlainURLs()` <a name="getPlainURLs"></a>

Возвращает урлы, непосредственно указанные в конфигурационном файле

#### Сигнатура

- **public** method.
- Возвращает `array` value.

### `getAllURLs()` <a name="getAllURLs"></a>

Возращает все собранные урлы

#### Сигнатура

- **public** method.
- Возвращает `array` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - [`ReflectionException`](http://php.net/class.ReflectionException)


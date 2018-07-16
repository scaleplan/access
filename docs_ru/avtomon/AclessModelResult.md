<small>avtomon</small>

AclessModelResult
=================

Класс результата выполнения модели

Описание
-----------

Class AclessModelResult

Сигнатура
---------

- **class**.
- Является подклассом класса `avtomon\DbResultItem`.

Свойства
----------

class устанавливает следующие свойства:

- [`$class`](#$class) &mdash; Отражение класса модели
- [`$method`](#$method) &mdash; Отражение метода модели
- [`$property`](#$property) &mdash; Отражение свойства модели
- [`$args`](#$args) &mdash; Аргументы выполнения
- [`$isPlainArgs`](#$isPlainArgs) &mdash; true - метод модели принимает аргументы в виде набора
false - в виде ассоциативного массива

### `$class` <a name="class"></a>

Отражение класса модели

#### Сигнатура

- **protected** property.
- Может быть одного из следующих типов:
    - `null`
    - [`ReflectionClass`](http://php.net/class.ReflectionClass)

### `$method` <a name="method"></a>

Отражение метода модели

#### Сигнатура

- **protected** property.
- Может быть одного из следующих типов:
    - `null`
    - [`ReflectionMethod`](http://php.net/class.ReflectionMethod)

### `$property` <a name="property"></a>

Отражение свойства модели

#### Сигнатура

- **protected** property.
- Может быть одного из следующих типов:
    - `null`
    - [`ReflectionProperty`](http://php.net/class.ReflectionProperty)

### `$args` <a name="args"></a>

Аргументы выполнения

#### Сигнатура

- **protected** property.
- Значение `array`.

### `$isPlainArgs` <a name="isPlainArgs"></a>

true - метод модели принимает аргументы в виде набора
false - в виде ассоциативного массива

#### Сигнатура

- **protected** property.
- Значение `bool`.

Методы
-------

Методы класса class:

- [`__construct()`](#__construct) &mdash; AclessModelResult constructor
- [`getClass()`](#getClass) &mdash; Геттер для отражения класса модели
- [`getMethod()`](#getMethod) &mdash; Геттер для отражения метода модели
- [`getProperty()`](#getProperty) &mdash; Геттер для отражения свойства модели
- [`getArgs()`](#getArgs) &mdash; Геттер для аргументов выполнения
- [`getIsPlainArgs()`](#getIsPlainArgs) &mdash; Будут ли параметры выполнения загружаться в виде последовательности аргументов
- [`setRawResult()`](#setRawResult) &mdash; Добавить результат из другого объекта DbResultItem
- [`checkDocReturn()`](#checkDocReturn) &mdash; Проверить тип возвращаемого значения по типам заданным в DOCBLOCK

### `__construct()` <a name="__construct"></a>

AclessModelResult constructor

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$class` ([`ReflectionClass`](http://php.net/class.ReflectionClass)) - отражение класса модели
    - `$method` ([`ReflectionMethod`](http://php.net/class.ReflectionMethod)) - отражение метода модели
    - `$property` ([`ReflectionProperty`](http://php.net/class.ReflectionProperty)) - отражение свойства модели
    - `$args` (`array`) - аргументы выполнения
    - `$isPlainArgs` (`bool`) - true - метод модели принимает аргументы в виде набора, false - в виде ассоциативного массива
    - `$result` (`null`|`mixed`) - результат
- Ничего не возвращает.

### `getClass()` <a name="getClass"></a>

Геттер для отражения класса модели

#### Сигнатура

- **public** method.
- Может возвращать одно из следующих значений:
    - `null`
    - [`ReflectionClass`](http://php.net/class.ReflectionClass)

### `getMethod()` <a name="getMethod"></a>

Геттер для отражения метода модели

#### Сигнатура

- **public** method.
- Может возвращать одно из следующих значений:
    - `null`
    - [`ReflectionMethod`](http://php.net/class.ReflectionMethod)

### `getProperty()` <a name="getProperty"></a>

Геттер для отражения свойства модели

#### Сигнатура

- **public** method.
- Может возвращать одно из следующих значений:
    - `null`
    - [`ReflectionProperty`](http://php.net/class.ReflectionProperty)

### `getArgs()` <a name="getArgs"></a>

Геттер для аргументов выполнения

#### Сигнатура

- **public** method.
- Может возвращать одно из следующих значений:
    - `array`
    - `null`

### `getIsPlainArgs()` <a name="getIsPlainArgs"></a>

Будут ли параметры выполнения загружаться в виде последовательности аргументов

#### Сигнатура

- **public** method.
- Возвращает `bool` value.

### `setRawResult()` <a name="setRawResult"></a>

Добавить результат из другого объекта DbResultItem

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$rawResult` (`avtomon\DbResultItem`|`null`)
- Ничего не возвращает.

### `checkDocReturn()` <a name="checkDocReturn"></a>

Проверить тип возвращаемого значения по типам заданным в DOCBLOCK

#### Сигнатура

- **public** method.
- Ничего не возвращает.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)


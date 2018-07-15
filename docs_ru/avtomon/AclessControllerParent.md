<small>avtomon</small>

AclessControllerParent
======================

Родитель для контроллеров - проверка прав доступа, фильтрация параметров

Описание
-----------

Class AclessControllerParent

Сигнатура
---------

- **abstract class**.

Свойства
----------

abstract class устанавливает следующие свойства:

- [`$before`](#$before) &mdash; Функции для выполнения перед исполнением метода контроллера
- [`$beforeDefaultResult`](#$beforeDefaultResult) &mdash; Результат выполения before-функции по умолчанию
- [`$after`](#$after) &mdash; Функции для выполнения после исполнения метода контроллера
- [`$afterDefaultResult`](#$afterDefaultResult) &mdash; Результат выполения after-функции по умолчанию

### `$before` <a name="before"></a>

Функции для выполнения перед исполнением метода контроллера

#### Сигнатура

- **protected static** property.
- Значение `array`.

### `$beforeDefaultResult` <a name="beforeDefaultResult"></a>

Результат выполения before-функции по умолчанию

#### Сигнатура

- **public static** property.
- Значение `mixed`.

### `$after` <a name="after"></a>

Функции для выполнения после исполнения метода контроллера

#### Сигнатура

- **protected static** property.
- Значение `array`.

### `$afterDefaultResult` <a name="afterDefaultResult"></a>

Результат выполения after-функции по умолчанию

#### Сигнатура

- **public static** property.
- Значение `mixed`.

Методы
-------

Методы класса abstract class:

- [`pushBefore()`](#pushBefore) &mdash; Добавить функцию в конец массива функций выполняемых перед исполнением метода контроллера
- [`unshiftBefore()`](#unshiftBefore) &mdash; Добавить функцию в начало массива функций выполняемых перед исполнением метода контроллера
- [`insertBefore()`](#insertBefore) &mdash; Добавить функцию в заданную позицию массива функций выполняемых перед исполнением метода контроллера
- [`pushAfter()`](#pushAfter) &mdash; Добавить функцию в конец массива функций выполняемых после исполнения метода контроллера
- [`unshiftAfter()`](#unshiftAfter) &mdash; Добавить функцию в начало массива функций выполняемых после исполнения метода контроллера
- [`insertAfter()`](#insertAfter) &mdash; Добавить функцию в заданную позицию массива функций выполняемых после исполнения метода контроллера
- [`removeBefore()`](#removeBefore) &mdash; Удалить функцию или все функции, которые должны выполняться перед исполненим метода контроллера
- [`removeAfter()`](#removeAfter) &mdash; Удалить функцию или все функции, которые должны выполняться после исполнения метода контроллера
- [`checkControllerMethod()`](#checkControllerMethod) &mdash; Проверка прав доступа и входных данных для метода
- [`executeBeforeHandlers()`](#executeBeforeHandlers) &mdash; Выполнить обработчики начала выполнения запроса
- [`executeAfterHandlers()`](#executeAfterHandlers) &mdash; Выполнить обработчики окончания выполнения запроса
- [`__callStatic()`](#__callStatic) &mdash; Проверка прав доступа и входных данных для статических методов
- [`__call()`](#__call) &mdash; Проверка прав доступа и входных данных для нестатических методов

### `pushBefore()` <a name="pushBefore"></a>

Добавить функцию в конец массива функций выполняемых перед исполнением метода контроллера

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$function` (`callable`) &mdash; - функция
- Ничего не возвращает.

### `unshiftBefore()` <a name="unshiftBefore"></a>

Добавить функцию в начало массива функций выполняемых перед исполнением метода контроллера

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$function` (`callable`) &mdash; - функция
- Ничего не возвращает.

### `insertBefore()` <a name="insertBefore"></a>

Добавить функцию в заданную позицию массива функций выполняемых перед исполнением метода контроллера

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$index` (`int`) &mdash; - позиция вставки
    - `$function` (`callable`) &mdash; - функция
- Ничего не возвращает.

### `pushAfter()` <a name="pushAfter"></a>

Добавить функцию в конец массива функций выполняемых после исполнения метода контроллера

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$function` (`callable`) &mdash; - функция
- Ничего не возвращает.

### `unshiftAfter()` <a name="unshiftAfter"></a>

Добавить функцию в начало массива функций выполняемых после исполнения метода контроллера

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$function` (`callable`) &mdash; - функция
- Ничего не возвращает.

### `insertAfter()` <a name="insertAfter"></a>

Добавить функцию в заданную позицию массива функций выполняемых после исполнения метода контроллера

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$index` (`int`) &mdash; - позиция вставки
    - `$function` (`callable`) &mdash; - функция
- Ничего не возвращает.

### `removeBefore()` <a name="removeBefore"></a>

Удалить функцию или все функции, которые должны выполняться перед исполненим метода контроллера

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$index` (`int`) &mdash; - позиция удаления
- Ничего не возвращает.

### `removeAfter()` <a name="removeAfter"></a>

Удалить функцию или все функции, которые должны выполняться после исполнения метода контроллера

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$index` (`int`) &mdash; - позиция удаления
- Ничего не возвращает.

### `checkControllerMethod()` <a name="checkControllerMethod"></a>

Проверка прав доступа и входных данных для метода

#### Сигнатура

- **protected static** method.
- Может принимать следующий параметр(ы):
    - `$methodName` (`string`) &mdash; - имя метода
    - `$args` (`array`) &mdash; - аргументы выполнения
    - `$obj` (`object`) &mdash; - объект, к контекте которого должен выполниться метод (если нестатический)
- Возвращает `avtomon\AbstractResult` value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - `avtomon\DbResultItemException`
    - [`ReflectionException`](http://php.net/class.ReflectionException)

### `executeBeforeHandlers()` <a name="executeBeforeHandlers"></a>

Выполнить обработчики начала выполнения запроса

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$method` ([`ReflectionMethod`](http://php.net/class.ReflectionMethod)|`null`) &mdash; - отражение метода, который будет выполняться
    - `$args` (`array`) &mdash; - его аргументы
- Возвращает `mixed` value.

### `executeAfterHandlers()` <a name="executeAfterHandlers"></a>

Выполнить обработчики окончания выполнения запроса

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$method` ([`ReflectionMethod`](http://php.net/class.ReflectionMethod)) &mdash; - отражение выполнявшегося метода констроллера
    - `$args` (`array`) &mdash; - его аргументы
    - `$result` (`null`) &mdash; - результат выполнения
- Возвращает `mixed` value.

### `__callStatic()` <a name="__callStatic"></a>

Проверка прав доступа и входных данных для статических методов

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$methodName` (`string`) &mdash; - имя метода или SQL-свойства
    - `$args` (`array`) &mdash; - массив аргументов
- Возвращает `avtomon\AbstractResult` value.

### `__call()` <a name="__call"></a>

Проверка прав доступа и входных данных для нестатических методов

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$methodName` (`string`) &mdash; - имя метода или SQL-свойства
    - `$args` (`array`) &mdash; - массив аргументов
- Возвращает `avtomon\AbstractResult` value.


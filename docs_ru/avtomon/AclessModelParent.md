<small>avtomon</small>

AclessServiceParent
=================

Родитель для моделей - для проверки аргументов

Описание
-----------

Class AclessServiceParent

Сигнатура
---------

- **class**.

Методы
-------

Методы класса class:

- [`checkServiceMethodEssence()`](#checkServiceMethodEssence) &mdash; Аудит метода или свойства, и выполнение для методов
- [`__callStatic()`](#__callStatic) &mdash; Проверка переданных аргументов для метода или SQL-свойства в статическом контексте
- [`__call()`](#__call) &mdash; Проверка переданных аргументов для метода или SQL-свойства

### `checkServiceMethodEssence()` <a name="checkServiceMethodEssence"></a>

Аудит метода или свойства, и выполнение для методов

#### Сигнатура

- **protected static** method.
- Может принимать следующий параметр(ы):
    - `$methodName` (`string`) - имя метода
    - `$args` (`array`) - аргументы
- Возвращает [`AclessServiceResult`](../avtomon/AclessServiceResult.md) value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - [`ReflectionException`](http://php.net/class.ReflectionException)

### `__callStatic()` <a name="__callStatic"></a>

Проверка переданных аргументов для метода или SQL-свойства в статическом контексте

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$methodName` (`string`) - имя метода или SQL-свойства
    - `$args` (`array`) - массив аргументов
- Возвращает [`AclessServiceResult`](../avtomon/AclessServiceResult.md) value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - [`ReflectionException`](http://php.net/class.ReflectionException)

### `__call()` <a name="__call"></a>

Проверка переданных аргументов для метода или SQL-свойства

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$methodName` (`string`) - имя метода или SQL-свойства
    - `$args` (`array`) - массив аргументов
- Возвращает [`AclessServiceResult`](../avtomon/AclessServiceResult.md) value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - [`ReflectionException`](http://php.net/class.ReflectionException)


<small>avtomon</small>

AclessModelParent
=================

Родитель для моделей - для проверки аргументов

Описание
-----------

Class AclessModelParent

Сигнатура
---------

- **class**.

Методы
-------

Методы класса class:

- [`checkModelMethodEssence()`](#checkModelMethodEssence) &mdash; Аудит метода или свойства, и выполнение для методов
- [`__callStatic()`](#__callStatic) &mdash; Проверка переданных аргументов для метода или SQL-свойства в статическом контексте
- [`__call()`](#__call) &mdash; Проверка переданных аргументов для метода или SQL-свойства

### `checkModelMethodEssence()` <a name="checkModelMethodEssence"></a>

Аудит метода или свойства, и выполнение для методов

#### Сигнатура

- **protected static** method.
- Может принимать следующий параметр(ы):
    - `$methodName` (`string`) &mdash; - имя метода
    - `$args` (`array`) &mdash; - аргументы
- Возвращает [`AclessModelResult`](../avtomon/AclessModelResult.md) value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - [`ReflectionException`](http://php.net/class.ReflectionException)

### `__callStatic()` <a name="__callStatic"></a>

Проверка переданных аргументов для метода или SQL-свойства в статическом контексте

#### Сигнатура

- **public static** method.
- Может принимать следующий параметр(ы):
    - `$methodName` (`string`) &mdash; - имя метода или SQL-свойства
    - `$args` (`array`) &mdash; - массив аргументов
- Возвращает [`AclessModelResult`](../avtomon/AclessModelResult.md) value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - [`ReflectionException`](http://php.net/class.ReflectionException)

### `__call()` <a name="__call"></a>

Проверка переданных аргументов для метода или SQL-свойства

#### Сигнатура

- **public** method.
- Может принимать следующий параметр(ы):
    - `$methodName` (`string`) &mdash; - имя метода или SQL-свойства
    - `$args` (`array`) &mdash; - массив аргументов
- Возвращает [`AclessModelResult`](../avtomon/AclessModelResult.md) value.
- Выбрасывает одно из следующих исключений:
    - [`avtomon\AclessException`](../avtomon/AclessException.md)
    - [`ReflectionException`](http://php.net/class.ReflectionException)


<small> avtomon </small>

AclessServiceParent
=================

Parent for models - to test the arguments

Description
-----------

Class AclessServiceParent

Signature
---------

- **class**.

Methods
-------

Class methods class:

  - [`checkServiceMethodEssence()`](#checkServiceMethodEssence) &mdash; Audit a method or properties, and execute for methods
  - [`__callStatic()`](#__callStatic) &mdash; Checking the passed arguments for a method or SQL property in a static context
  - [`__call()`](#__call) &mdash; Checking the passed arguments for the method or the SQL property

### `checkServiceMethodEssence()`<a name="checkServiceMethodEssence"> </a>

Audit a method or properties, and execute for methods

#### Signature

- **protected static** method.
- It can take the following parameter (s):
  - `$methodName`(`string`) - method name
  - `$args`(`array`) - arguments
- Returns [`AclessServiceResult`](../avtomon/AclessServiceResult.md) value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)
  - [`ReflectionException`](http://php.net/class.ReflectionException)

### `__callStatic()`<a name="__callStatic"> </a>

Checking the passed arguments for a method or SQL property in a static context

#### Signature

- **public static** method.
- It can take the following parameter (s):
  - `$methodName`(`string`) - method name or SQL properties
  - `$args`(`array`) - array of arguments
- Returns [`AclessServiceResult`](../avtomon/AclessServiceResult.md) value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)
  - [`ReflectionException`](http://php.net/class.ReflectionException)

### `__call()`<a name="__call"> </a>

Checking the passed arguments for the method or the SQL property

#### Signature

- **public** method.
- It can take the following parameter (s):
  - `$methodName`(`string`) - method name or SQL properties
  - `$args`(`array`) - array of arguments
- Returns [`AclessServiceResult`](../avtomon/AclessServiceResult.md) value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)
  - [`ReflectionException`](http://php.net/class.ReflectionException)


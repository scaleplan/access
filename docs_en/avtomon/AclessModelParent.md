the <small>avtomon</small>

AclessModelParent
=================

Parent for models-to test arguments

Description
-----------

Class AclessModelParent

Signature
---------

- **class**.

Methods
-------

Class methods class:

  - [`checkModelMethodEssence()`](#checkModelMethodEssence) &mdash; the Audit method or property, and the implementation for the methods
- ['__callStatic()'](#___callStatic) &mdash; Check the passed arguments for a method or SQL property in a static context
- ['__call (`' ](#___call) &mdash; Check the passed arguments for a method or SQL property

### `checkModelMethodEssence (`'<a name= "checkModelMethodEssence" ></a>

The audit method or property, and the implementation for the methods

#### Signature

- **protected static * * method.
- Can take the following parameter (s):
    - '$methodName ' ('string`) &mdash; - method name
    	- `$args `(`array') &mdash; - arguments
- Return [`AclessModelResult`](../avtomon/AclessModelResult.md) value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - ['ReflectionException'](http://php.net/class.ReflectionException)

### `__callStatic() '<a name= "__callStatic " ></a>

Checking the passed arguments for a method or SQL property in a static context

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$methodName ' ('string`) &mdash; - method name or SQL property
    - '$args ' ('array`) &mdash; - array of arguments
- Return [`AclessModelResult`](../avtomon/AclessModelResult.md) value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - ['ReflectionException'](http://php.net/class.ReflectionException)

### `__call`) '<a name= "__call " ></a>

Check the passed arguments for a method or SQL property

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$methodName ' ('string`) &mdash; - method name or SQL property
    - '$args ' ('array`) &mdash; - array of arguments
- Return [`AclessModelResult`](../avtomon/AclessModelResult.md) value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - ['ReflectionException'](http://php.net/class.ReflectionException)


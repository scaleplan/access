<small>avtomon </small>

AclessModelParent
=================

Parentfor models - to test thearguments

Description
-----------

Class AclessModelParent

Signature
---------

-** class**.

Methods
-------

Classmethodsclass:

-[`checkModelMethodEssence()`](#checkModelMethodEssence) &mdash; Audit a method or properties, and execute formethods
-[`__callStatic()`](#__callStatic) &mdash; Checking the passed arguments for a method or SQL property in a staticcontext
-[`__call()`](#__call) &mdash; Checking the passed arguments for the method or the SQLproperty

###`checkModelMethodEssence()`<a name="checkModelMethodEssence"></a>

Audita method or properties, and execute formethods

#### Signature

-** protected static **method.
-It can take the following parameter(s):
-`$methodName`(`string`) &mdash; - methodname
-`$args`(`array`) &mdash; -Arguments
-Returns [`AclessModelResult`](../ avtomon/AclessModelResult.md)value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)
  - [`ReflectionException`](http://php.net/class.ReflectionException)

###`__callStatic()`<a name="__callStatic"></a>

Checkingthe passed arguments for a method or SQL property in a staticcontext

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$methodName`(`string`) &mdash; - method name or SQLproperties
-`$args`(`array`) &mdash; - array ofarguments
-Returns [`AclessModelResult`](../ avtomon/AclessModelResult.md)value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)
  - [`ReflectionException`](http://php.net/class.ReflectionException)

###`__call()`<a name="__call"></a>

Checkingthe passed arguments for the method or the SQLproperty

#### Signature

-** public **method.
-It can take the following parameter(s):
-`$methodName`(`string`) &mdash; - method name or SQLproperties
-`$args`(`array`) &mdash; - array ofarguments
-Returns [`AclessModelResult`](../ avtomon/AclessModelResult.md)value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)
  - [`ReflectionException`](http://php.net/class.ReflectionException)


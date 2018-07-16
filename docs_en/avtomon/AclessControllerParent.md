<small> avtomon </small>

AclessControllerParent
======================

Parent for controllers - checking access rights, filtering parameters

Description
-----------

Class AclessControllerParent

Signature
---------

- **abstract class**.

Properties
----------

The abstract class sets the following properties:

  - [`$before`](#$before) &mdash; Functions to execute before executing the controller method
  - [`$beforeDefaultResult`](#$beforeDefaultResult) &mdash; The result of the default before function
  - [`$after`](#$after) &mdash; Functions for execution after execution of the controller method
  - [`$afterDefaultResult`](#$afterDefaultResult) &mdash; Result of performing the after-function by default

### `$before`<a name="before"> </a>

Functions to execute before executing the controller method

#### Signature

**protected static** property.
- The value of `array`.

### `$beforeDefaultResult`<a name="beforeDefaultResult"> </a>

The result of the default before function

#### Signature

- **public static** property.
- The value of `mixed`.

### `$after`<a name="after"> </a>

Functions for execution after execution of the controller method

#### Signature

**protected static** property.
- The value of `array`.

### `$afterDefaultResult`<a name="afterDefaultResult"> </a>

Result of performing the after-function by default

#### Signature

- **public static** property.
- The value of `mixed`.

Methods
-------

Abstract class methods:

  - [`pushBefore()`](#pushBefore) &mdash; Add a function to the end of the array of functions performed before the execution of the controller method
  - [`unshiftBefore()`](#unshiftBefore) &mdash; Add a function to the beginning of the array of functions performed before the execution of the controller method
  - [`insertBefore()`](#insertBefore) &mdash; Add a function to the specified position of the array of functions performed before the execution of the controller method
  - [`pushAfter()`](#pushAfter) &mdash; Add a function to the end of an array of functions executed after the execution of the controller method
  - [`unshiftAfter()`](#unshiftAfter) &mdash; Add a function to the beginning of an array of functions executed after the execution of the controller method
  - [`insertAfter()`](#insertAfter) &mdash; Add the function to the specified position of the array of functions executed after the execution of the controller method
  - [`removeBefore()`](#removeBefore) &mdash; Delete a function or all functions that must be performed before executing the controller method
  - [`removeAfter()`](#removeAfter) &mdash; Delete a function or all functions that must be performed after the execution of the controller method
  - [`checkControllerMethod()`](#checkControllerMethod) &mdash; Verifying access rights and input for the method
  - [`executeBeforeHandlers()`](#executeBeforeHandlers) &mdash; Execute handlers for starting the query execution
  - [`executeAfterHandlers()`](#executeAfterHandlers) &mdash; Execute output handlers for the execution of the request
  - [`__callStatic()`](#__callStatic) &mdash; Validating access rights and input data for static methods
  - [`__call()`](#__call) &mdash; Validating access rights and input data for non-static methods

### `pushBefore()`<a name="pushBefore"> </a>

Add a function to the end of the array of functions performed before the execution of the controller method

#### Signature

- **public static** method.
- It can take the following parameter (s):
  - `$function`(`callable`) &mdash; - function
- Returns nothing.

### `unshiftBefore()`<a name="unshiftBefore"> </a>

Add a function to the beginning of the array of functions performed before the execution of the controller method

#### Signature

- **public static** method.
- It can take the following parameter (s):
  - `$function`(`callable`) &mdash; - function
- Returns nothing.

### `insertBefore()`<a name="insertBefore"> </a>

Add a function to the specified position of the array of functions performed before the execution of the controller method

#### Signature

- **public static** method.
- It can take the following parameter (s):
  - `$index`(`int`) &mdash; - Insertion position
  - `$function`(`callable`) &mdash; - function
- Returns nothing.

### `pushAfter()`<a name="pushAfter"> </a>

Add a function to the end of an array of functions executed after the execution of the controller method

#### Signature

- **public static** method.
- It can take the following parameter (s):
  - `$function`(`callable`) &mdash; - function
- Returns nothing.

### `unshiftAfter()`<a name="unshiftAfter"> </a>

Add a function to the beginning of an array of functions executed after the execution of the controller method

#### Signature

- **public static** method.
- It can take the following parameter (s):
  - `$function`(`callable`) &mdash; - function
- Returns nothing.

### `insertAfter()`<a name="insertAfter"> </a>

Add the function to the specified position of the array of functions executed after the execution of the controller method

#### Signature

- **public static** method.
- It can take the following parameter (s):
  - `$index`(`int`) &mdash; - Insertion position
  - `$function`(`callable`) &mdash; - function
- Returns nothing.

### `removeBefore()`<a name="removeBefore"> </a>

Delete a function or all functions that must be performed before executing the controller method

#### Signature

- **public static** method.
- It can take the following parameter (s):
  - `$index`(`int`) &mdash; - delete position
- Returns nothing.

### `removeAfter()`<a name="removeAfter"> </a>

Delete a function or all functions that must be performed after the execution of the controller method

#### Signature

- **public static** method.
- It can take the following parameter (s):
  - `$index`(`int`) &mdash; - delete position
- Returns nothing.

### `checkControllerMethod()`<a name="checkControllerMethod"> </a>

Verifying access rights and input for the method

#### Signature

- **protected static** method.
- It can take the following parameter (s):
  - `$methodName`(`string`) &mdash; - method name
  - `$args`(`array`) &mdash; - execution arguments
  - `$obj`(`object`) &mdash; - an object to the context of which the method should be executed (if not static)
- Returns `avtomon\AbstractResult`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)
  - `avtomon\DbResultItemException`
  - [`ReflectionException`](http://php.net/class.ReflectionException)

### `executeBeforeHandlers()`<a name="executeBeforeHandlers"> </a>

Execute handlers for starting the query execution

#### Signature

- **public static** method.
- It can take the following parameter (s):
  - `$method`([`ReflectionMethod`](http://php.net/class.ReflectionMethod) | `null`) &mdash; - reflection of the method to be performed
  - `$args`(`array`) &mdash; - his arguments
Returns the `mixed`value.

### `executeAfterHandlers()`<a name="executeAfterHandlers"> </a>

Execute output handlers for the execution of the request

#### Signature

- **public static** method.
- It can take the following parameter (s):
  - `$method`([`ReflectionMethod`](http://php.net/class.ReflectionMethod)) &mdash; - Reflection of the executed method of the Constellor
  - `$args`(`array`) &mdash; - his arguments
  - `$result`(`null`) &mdash; - result of performance
Returns the `mixed`value.

### `__callStatic()`<a name="__callStatic"> </a>

Validating access rights and input data for static methods

#### Signature

- **public static** method.
- It can take the following parameter (s):
  - `$methodName`(`string`) &mdash; - method name or SQL properties
  - `$args`(`array`) &mdash; - array of arguments
- Returns `avtomon\AbstractResult`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)
  - `avtomon\DbResultItemException`
  - [`ReflectionException`](http://php.net/class.ReflectionException)

### `__call()`<a name="__call"> </a>

Validating access rights and input data for non-static methods

#### Signature

- **public** method.
- It can take the following parameter (s):
  - `$methodName`(`string`) &mdash; - method name or SQL properties
  - `$args`(`array`) &mdash; - array of arguments
- Returns `avtomon\AbstractResult`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)
  - `avtomon\DbResultItemException`
  - [`ReflectionException`](http://php.net/class.ReflectionException)


the <small>avtomon</small>

AclessControllerParent
======================

Parent for controllers - check access rights, filter parameters

Description
-----------

Class AclessControllerParent

Signature
---------

- **abstract class**.

Properties
----------

the abstract class sets the following properties:

- ['$before'](#$before) &mdash; Functions to execute before controller method execution
- ['$beforeDefaultResult'](#$beforeDefaultResult) &mdash; the result of executing before-default function
- ['$after'](#$after) &mdash; Functions to execute after the controller method
- ['$afterDefaultResult'](#$afterDefaultResult) &mdash; the result of executing the default after function

### '$before '<a name= "before"></a>

Functions to perform before executing the controller method

#### Signature

- **protected static * * property.
- Value 'array'.

### `$beforeDefaultResult '<a name= "beforeDefaultResult" ></a>

The result of performing before-the default function

#### Signature

- **public static * * property.
- Value 'mixed'.

### '$after '<a name= "after" ></a>

Function to execute after the execution of the controller method

#### Signature

- **protected static * * property.
- Value 'array'.

### '$afterDefaultResult '<a name= "afterDefaultResult" ></a>

The result of the execution after the default options

#### Signature

- **public static * * property.
- Value 'mixed'.

Methods
-------

Methods of the abstract class:

  - [`pushBefore()`](#pushBefore) &mdash; add the function to the end of the array of functions performed before the execution of the controller method
- ['unshiftBefore()`](#unshiftefore) &mdash; Add a function to the beginning of the array of functions executed before the controller method execution
- ['insertBefore()'](#insertBefore) &mdash; Add a function to the given position of the array of functions executed before the controller method execution
  - [`pushAfter()`](#pushAfter) &mdash; add the function to the end of the array of functions performed upon execution of the controller method
- ['unshiftAfter()'](#unshiftAfter) &mdash; Add a function to the beginning of the array of functions executed after the execution of the controller method
- ['insertAfter (`' ](#insertAfter) &mdash; Add a function to the given position of the array of functions executed after the controller method execution
- ['removeBefore()'](#removeBefore) &mdash; Remove a function or all functions that must be executed before executing a controller method
- ['removeAfter()'](#removeAfter) &mdash; Remove a function or all functions that must be executed after the controller method execution
- ['checkControllerMethod()'](#checkControllerMethod) &mdash; Check access permissions and input data for the method
  - [`executeBeforeHandlers()`](#executeBeforeHandlers) &mdash; Execute handlers beginning of the query
  - [`executeAfterHandlers()`](#executeAfterHandlers) &mdash; Execute the handlers have completed execution of the request
- ['__callStatic()'](#___callStatic) &mdash; checking access rights And input for static methods
- ['__call()'](#___call) &mdash; checking permissions and input for non-static methods

### 'pushBefore()' <a name= 'pushBefore' ></a>

Add a function to the end of the array of functions performed before executing the controller method

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$function ' ('callable') &mdash; - function
- It doesn't make it back.

### `unshiftefore() '<a name= 'unshiftefore' ></a>

Add a function to the beginning of the array of functions performed before the controller method execution

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$function ' ('callable') &mdash; - function
- It doesn't make it back.

### 'insertBefore()' <a name= 'insertBefore' ></a>

Add a function to the specified position of the array of functions performed before the execution of the controller method

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$index ' ('int') &mdash; - insert position
    - '$function ' ('callable') &mdash; - function
- It doesn't make it back.

### 'pushAfter()' <a name= "pushAfter" ></a>

Add a function to the end of the array of functions performed after the controller method execution

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$function ' ('callable') &mdash; - function
- It doesn't make it back.

### `unshiftAfter() '<a name= "unshiftAfter" ></a>

Add a function to the beginning of the array of functions performed after the controller method execution

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$function ' ('callable') &mdash; - function
- It doesn't make it back.

### 'insertAfter()' <a name= 'insertAfter' ></a>

Add a function to the specified position of the array of functions executed after the execution of the controller method

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$index ' ('int') &mdash; - insert position
    - '$function ' ('callable') &mdash; - function
- It doesn't make it back.

### 'removeBefore()' <a name= "removeBefore" ></a>

Remove the function or all functions that must be executed before the controller method is executed

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$index ' ('int`) &mdash; - delete position
- It doesn't make it back.

### 'removeAfter()' <a name= "removeAfter" ></a>

Remove a function or all functions that must be executed after the controller method is executed

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$index ' ('int`) &mdash; - delete position
- It doesn't make it back.

### `checkControllerMethod() '<a name= "checkControllerMethod" ></a>

Checking access rights and input for a method

#### Signature

- **protected static * * method.
- Can take the following parameter (s):
    - '$methodName ' ('string`) &mdash; - method name
    - '$args ' ('array`) &mdash; - execution arguments
    - '$obj ' ('object`) &mdash; - object, to the context of which the method should be executed (if non-static)
- Returns `avtomon\AbstractResult`value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - `avtomon\DbResultItemException`
    - ['ReflectionException'](http://php.net/class.ReflectionException)

### 'executeBeforeHandlers (`'<a name= "executeBeforeHandlers" ></a>

To run the processors start the execution of the query

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$method ' (['ReflectionMethod']) (http://php.net/class.ReflectionMethod)| 'null') &mdash; - reflection of the method to be executed
    - '$args ' ('array`) &mdash; - its arguments
- Returns 'mixed' value.

### 'executeAfterHandlers (`'<a name= "executeAfterHandlers" ></a>

Execute the handlers have completed execution of the request

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    	- `$method`([`ReflectionMethod`](http://php.net/class.ReflectionMethod)) &mdash; - the reflection performed by the method of controller
    - '$args ' ('array`) &mdash; - its arguments
    - '$result ' ('null`) &mdash; - execution result
- Returns 'mixed' value.

### `__callStatic() '<a name= "__callStatic " ></a>

Checking permissions and input for static methods

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$methodName ' ('string`) &mdash; - method name or SQL property
    - '$args ' ('array`) &mdash; - array of arguments
- Returns `avtomon\AbstractResult`value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - `avtomon\DbResultItemException`
    - ['ReflectionException'](http://php.net/class.ReflectionException)

### `__call`) '<a name= "__call " ></a>

Checking permissions and input for non-static methods

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$methodName ' ('string`) &mdash; - method name or SQL property
    - '$args ' ('array`) &mdash; - array of arguments
- Returns `avtomon\AbstractResult`value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - `avtomon\DbResultItemException`
    - ['ReflectionException'](http://php.net/class.ReflectionException)


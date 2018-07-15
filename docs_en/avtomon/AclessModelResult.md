the <small>avtomon</small>

AclessModelResult
=================

Class the result of running the model

Description
-----------

Class AclessModelResult

Signature
---------

- **class**.
- Is a subclass of the class `avtomon\DbResultItem`.

Properties
----------

class sets the following properties:

- ['$class'](#$class) &mdash; Reflection of model class
- ['$method'](#$method) &mdash; Reflection of the model method
  - [`$property`](#$property) &mdash; a reflection of the properties of the model
- ['$args'](#$args) &mdash; execution Arguments
- ['$isPlainArgs'](#$isPlainArgs) &mdash; true-model method takes arguments as a set
false - in the form of an associative array

### '$class '<a name= "class" ></a>

The reflection of the model class

#### Signature

- **protected * * property.
- Can be one of the following types:
    - 'null`
    - ['ReflectionClass'](http://php.net/class.ReflectionClass)

### '$method '<a name= "method" ></a>

Reflection of the model method

#### Signature

- **protected * * property.
- Can be one of the following types:
    - 'null`
    - ['ReflectionMethod'](http://php.net/class.ReflectionMethod)

### '$property '<a name= "property" ></a>

The reflection properties of the model

#### Signature

- **protected * * property.
- Can be one of the following types:
    - 'null`
    - ['ReflectionProperty'](http://php.net/class.ReflectionProperty)

### '$args '<a name= "args" ></a>

Execution arguments

#### Signature

- **protected * * property.
- Value 'array'.

### '$isPlainArgs '<a name= "isPlainArgs" ></a>

true - model method takes arguments in the form of a set
false - in the form of an associative array

#### Signature

- **protected * * property.
- `Bool ' value.

Methods
-------

Class methods class:

  - [`__construct (`' ](#__construct) &mdash; AclessModelResult constructor
- ['getClass()'](#getClass) &mdash; Getter to reflect model class
- ['getMethod()'](#getMethod) &mdash; Getter to reflect model method
  - [`getProperty()`](#getProperty) &mdash; a Getter to reflect the properties of the model
- ['getArgs()'](#getArgs) &mdash; Getter for execution arguments
- ['getIsPlainArgs()'](#getIsPlainArgs) &mdash; Will the execution parameters be loaded as a sequence of arguments
- ['setRawResult()'](#setRawResult) &mdash; Add result from other DbResultItem object
- ['checkDocReturn()'](#checkDocReturn) &mdash; Check the return type by the types specified in DOCBLOCK

### `__construct() '<a name= "__construct " ></a>

AclessModelResult constructor

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$class ' (['ReflectionClass']) (http://php.net/class.ReflectionClass)) &mdash; - reflection of the model class
    - '$method ' (['ReflectionMethod']) (http://php.net/class.ReflectionMethod)) &mdash; - reflection of the model method
    - '$property ' (['ReflectionProperty']) (http://php.net/class.ReflectionProperty)) &mdash; - reflection of the model property
    - '$args ' ('array`) &mdash; - execution arguments
    - '$isPlainArgs ' ('bool`) &mdash; - true-the model method takes arguments as a set, false-as an associative array
    	- `$result`(`null`|`mixed`) &mdash; is the result
- It doesn't make it back.

### 'getClass()' <a name= 'getClass' ></a>

Getter to reflect model class

#### Signature

- **public * * method.
- Can return one of the following values:
    - 'null`
    - ['ReflectionClass'](http://php.net/class.ReflectionClass)

### 'getMethod()' <a name= "getMethod" ></a>

Getter to reflect the model method

#### Signature

- **public * * method.
- Can return one of the following values:
    - 'null`
    - ['ReflectionMethod'](http://php.net/class.ReflectionMethod)

### 'getProperty()' <a name= 'getProperty' ></a>

Getter to reflect model properties

#### Signature

- **public * * method.
- Can return one of the following values:
    - 'null`
    - ['ReflectionProperty'](http://php.net/class.ReflectionProperty)

### 'getArgs()' <a name= "getArgs" ></a>

Getter for execution arguments

#### Signature

- **public * * method.
- Can return one of the following values:
    - 'array`
    - 'null`

### 'getIsPlainArgs()' <a name= 'getIsPlainArgs' ></a>

Will the execution parameters be loaded as a sequence of arguments

#### Signature

- **public * * method.
- Returns `bool ' value.

### 'setRawResult()' <a name= "setRawResult" ></a>

Add result from another DbResultItem object

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$rawResult ' ('avtomon\DbResultItem`|' null`)
- It doesn't make it back.

### `checkDocReturn() '<a name= 'checkDocReturn' ></a>

Check the return type by the types specified in DOCBLOCK

#### Signature

- **public * * method.
- It doesn't make it back.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)


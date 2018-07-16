<small> avtomon </small>

AclessModelResult
=================

The result class of the model

Description
-----------

Class AclessModelResult

Signature
---------

- **class**.
- It is a subclass of the class `avtomon\DbResultItem`.

Properties
----------

class sets the following properties:

  - [`$class`](#$class) &mdash; Reflection of the model class
  - [`$method`](#$method) &mdash; Reflection of the model method
  - [`$property`](#$property) &mdash; Reflecting the model property
  - [`$args`](#$args) &mdash; Execution Arguments
  - [`$isPlainArgs`](#$isPlainArgs) &mdash; true - the model method takes arguments as a set
false - as an associative array

### `$class`<a name="class"> </a>

Reflection of the model class

#### Signature

- **protected** property.
- Can be one of the following types:
  - `null`
  - [`ReflectionClass`](http://php.net/class.ReflectionClass)

### `$method`<a name="method"> </a>

Reflection of the model method

#### Signature

- **protected** property.
- Can be one of the following types:
  - `null`
  - [`ReflectionMethod`](http://php.net/class.ReflectionMethod)

### `$property`<a name="property"> </a>

Reflecting the model property

#### Signature

- **protected** property.
- Can be one of the following types:
  - `null`
  - [`ReflectionProperty`](http://php.net/class.ReflectionProperty)

### `$args`<a name="args"> </a>

Execution Arguments

#### Signature

- **protected** property.
- The value of `array`.

### `$isPlainArgs`<a name="isPlainArgs"> </a>

true - the model method takes arguments as a set
false - as an associative array

#### Signature

- **protected** property.
- The value of `bool`.

Methods
-------

Class methods class:

  - [`__construct()`](#__construct) &mdash; AclessModelResult constructor
  - [`getClass()`](#getClass) &mdash; Getter for reflecting the model class
  - [`getMethod()`](#getMethod) &mdash; Getter to reflect the method of the model
  - [`getProperty()`](#getProperty) &mdash; Getter to reflect model properties
  - [`getArgs()`](#getArgs) &mdash; Getter for execution arguments
  - [`getIsPlainArgs()`](#getIsPlainArgs) &mdash; Will the execution parameters be loaded as a sequence of arguments
  - [`setRawResult()`](#setRawResult) &mdash; Add result from another object DbResultItem
  - [`checkDocReturn()`](#checkDocReturn) &mdash; Check the return type for the types specified in DOCBLOCK

### `__construct()`<a name="__construct"> </a>

AclessModelResult constructor

#### Signature

- **public** method.
- It can take the following parameter (s):
  - `$class`([`ReflectionClass`](http://php.net/class.ReflectionClass)) - reflection of the model class
  - `$method`([`ReflectionMethod`](http://php.net/class.ReflectionMethod)) - reflection of the model method
  - `$property`([`ReflectionProperty`](http://php.net/class.ReflectionProperty)) - reflection of the model property
  - `$args`(`array`) - execution arguments
  - `$isPlainArgs`(`bool`) - true - model method takes arguments as a set, false - as an associative array
  - `$result`(`null`| `mixed`) - result
- Returns nothing.

### `getClass()`<a name="getClass"> </a>

Getter for reflecting the model class

#### Signature

- **public** method.
- Can return one of the following values:
  - `null`
  - [`ReflectionClass`](http://php.net/class.ReflectionClass)

### `getMethod()`<a name="getMethod"> </a>

Getter to reflect the method of the model

#### Signature

- **public** method.
- Can return one of the following values:
  - `null`
  - [`ReflectionMethod`](http://php.net/class.ReflectionMethod)

### `getProperty()`<a name="getProperty"> </a>

Getter to reflect model properties

#### Signature

- **public** method.
- Can return one of the following values:
  - `null`
  - [`ReflectionProperty`](http://php.net/class.ReflectionProperty)

### `getArgs()`<a name="getArgs"> </a>

Getter for execution arguments

#### Signature

- **public** method.
- Can return one of the following values:
- array
  - `null`

### `getIsPlainArgs()`<a name="getIsPlainArgs"> </a>

Will the execution parameters be loaded as a sequence of arguments

#### Signature

- **public** method.
- Returns the `bool`value.

### `setRawResult()`<a name="setRawResult"> </a>

Add result from another object DbResultItem

#### Signature

- **public** method.
- It can take the following parameter (s):
  - `$rawResult`(`avtomon\DbResultItem`| `null`)
- Returns nothing.

### `checkDocReturn()`<a name="checkDocReturn"> </a>

Check the return type for the types specified in DOCBLOCK

#### Signature

- **public** method.
- Returns nothing.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)


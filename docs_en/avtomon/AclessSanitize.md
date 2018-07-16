<small> avtomon </small>

AclessSanitize
==============

Runtime Checker Class

Description
-----------

Class AclessSanitize

Signature
---------

- **class**.

Properties
----------

class sets the following properties:

  - [`$reflector`](#$reflector) &mdash; Method Reflection or SQL Properties
  - [`$args`](#$args) &mdash; Array of arguments
  - [`$sanitizedArgs`](#$sanitizedArgs) &mdash; Array of pointed arguments

### `$reflector`<a name="reflector"> </a>

Method Reflection or SQL Properties

#### Signature

- **protected** property.
- Can be one of the following types:
  - [`ReflectionMethod`](http://php.net/class.ReflectionMethod)
  - [`ReflectionProperty`](http://php.net/class.ReflectionProperty)

### `$args`<a name="args"> </a>

Array of arguments

#### Signature

- **protected** property.
- The value of `array`.

### `$sanitizedArgs`<a name="sanitizedArgs"> </a>

Array of pointed arguments

#### Signature

- **protected** property.
- Can be one of the following types:
- array
- `null`

Methods
-------

Class methods class:

  - [`__construct()`](#__construct) &mdash; Constructor
  - [`sanitizeArgs()`](#sanitizeArgs) &mdash; Check and clear arguments
  - [`sanitizeMethodArgs()`](#sanitizeMethodArgs) &mdash; Check method arguments
  - [`sanitizeSQLPropertyArgs()`](#sanitizeSQLPropertyArgs) &mdash; Check the arguments for the method property
  - [`argAvailabilityCheck()`](#argAvailabilityCheck) &mdash; Checking for arguments
  - [`getDocParams()`](#getDocParams) &mdash; Return an array of DOCBLOCK parameters and a subgroup of optional parameters
  - [`docTypeCheck()`](#docTypeCheck) &mdash; Checking the arguments to match the type
  - [`typeCheck()`](#typeCheck) &mdash; Checking the value for the type
  - [`getSQLParams()`](#getSQLParams) &mdash; Get all the parameters from the SQL query
  - [`camel2dashed()`](#camel2dashed) &mdash; Convert a string as camelCase to a string of the form dashed (camelCase - & gt; camel-case)

### `__construct()`<a name="__construct"> </a>

Constructor

#### Signature

- **public** method.
- It can take the following parameter (s):
	- `$reflector`([`Reflector`](http://php.net/class.Reflector)) &mdash; - method reflection or SQL properties
	- `$args`(`array`) &mdash; - array of arguments
- Returns nothing.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `sanitizeArgs()`<a name="sanitizeArgs"> </a>

Check and clear arguments

#### Signature

- **public** method.
- _Returns: _ - array of cleared arguments
- array
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `sanitizeMethodArgs()`<a name="sanitizeMethodArgs"> </a>

Check method arguments

#### Signature

- **public static** method.
- It can take the following parameter (s):
	- `$method`([`ReflectionMethod`](http://php.net/class.ReflectionMethod)) &mdash; - Reflection wrapper for method
	- `$args`(`array`) &mdash; - array of arguments
Returns the `array`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `sanitizeSQLPropertyArgs()`<a name="sanitizeSQLPropertyArgs"> </a>

Check the arguments for the method property

#### Signature

- **public static** method.
- It can take the following parameter (s):
	- `$property`([`ReflectionProperty`](http://php.net/class.ReflectionProperty)) &mdash; - Reflection-wrapper for SQL-property
	- `$args`(`array`) &mdash; - array of arguments
Returns the `array`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `argAvailabilityCheck()`<a name="argAvailabilityCheck"> </a>

Checking for arguments

#### Signature

- **protected static** method.
- It can take the following parameter (s):
	- `$paramName`(`string`) &mdash; - parameter name
	- `$args`(`array`) &mdash; - array of arguments
	- `$optionParams`(`array`) &mdash; - array of optional parameters
- Returns nothing.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `getDocParams()`<a name="getDocParams"> </a>

Return an array of DOCBLOCK parameters and a subgroup of optional parameters

#### Signature

- **protected static** method.
- It can take the following parameter (s):
	- `$docParams`(`array`) &mdash; - source array of parameters
Returns the `array`value.

### `docTypeCheck()`<a name="docTypeCheck"> </a>

Checking the arguments to match the type

#### Signature

- **protected static** method.
- It can take the following parameter (s):
	- `$arg`&mdash; - the value of the argument
	- `$paramName`(`string`) &mdash; - name of the argument/parameter
	- `$paramType`(`string`) &mdash; - required type or group of types
	- `$docBlock`(`phpDocumentor\Reflection\DocBlock`) &mdash; - reference to the DOCBLOCK object of the method or property
- Returns nothing.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `typeCheck()`<a name="typeCheck"> </a>

Checking the value for the type

#### Signature

- **public static** method.
- It can take the following parameter (s):
	- `$value`&mdash; - value
	- `$types`(`array`) &mdash; - accepted types
	- `$denyFuzzy`(`bool`) &mdash; - Is a strict comparison used?
- Returns the `bool`value.

### `getSQLParams()`<a name="getSQLParams"> </a>

Get all the parameters from the SQL query

#### Signature

- **public static** method.
- It can take the following parameter (s):
	- `$sql`
Returns the `array`value.

### `camel2dashed()`<a name="camel2dashed"> </a>

Convert a string as camelCase to a string of the form dashed (camelCase -> camel-case)

#### Signature

- **public static** method.
- It can take the following parameter (s):
	- `$str`(`string`) &mdash; - string in camelCase
Returns `string`value.


the <small>avtomon</small>

AclessSanitize
==============

Execution argument validation class

Description
-----------

Class AclessSanitize

Signature
---------

- **class**.

Properties
----------

class sets the following properties:

- ['$reflector`](#$reflector) &mdash; reflection method or SQL properties
- ['$args'](#$args) &mdash; array of arguments
- ['$sanitizedArgs'](#$sanitizedArgs) &mdash; array of arguments

### '$reflector '<a name= "reflector" ></a>

Reflection of a method or SQL property

#### Signature

- **protected * * property.
- Can be one of the following types:
    - ['ReflectionMethod'](http://php.net/class.ReflectionMethod)
    - ['ReflectionProperty'](http://php.net/class.ReflectionProperty)

### '$args '<a name= "args" ></a>

Array of arguments

#### Signature

- **protected * * property.
- Value 'array'.

### '$sanitizedArgs '<a name= "sanitizedArgs" ></a>

Cleaned an array of arguments

#### Signature

- **protected * * property.
- Can be one of the following types:
    - 'array`
    - 'null`

Methods
-------

Class methods class:

  - [`__construct (`' ](#__construct) &mdash; Constructor
- ['sanitizeArgs()'](#sanitizeArgs) &mdash; Check and clear arguments
- ['sanitizeMethodArgs()'](#sanitizeMethodArgs) &mdash; Check method arguments
  - [`sanitizeSQLPropertyArgs()`](#sanitizeSQLPropertyArgs) &mdash; Check the arguments to the property method
- ['argAvailabilityCheck (`' ](#argAvailabilityCheck) &mdash; Check for arguments
- ['getDocParams()'](#getDocParams) &mdash; Return an array of DOCBLOCK parameters and a subgroup of optional parameters
  - [`docTypeCheck()`](#docTypeCheck) &mdash; Check arguments for compliance with the type
- ['typeCheck (`' ](#typeCheck) &mdash; Check the value for type match
- ['getSQLParams()'](#getSQLParams) &mdash; Get all parameters from SQL query

### `__construct() '<a name= "__construct " ></a>

Designer

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$reflector ' (['Reflector']) (http://php.net/class.Reflector)) &mdash; - reflection of a method or SQL property
    - '$args ' ('array`) &mdash; - array of arguments
- It doesn't make it back.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### 'sanitizeArgs()' <a name= "sanitizeArgs" ></a>

Check and clear arguments

#### Signature

- **public * * method.
- Returns 'array' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### 'sanitizeMethodArgs()' <a name= "sanitizeMethodArgs" ></a>

Check method arguments

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$method ' (['ReflectionMethod']) (http://php.net/class.ReflectionMethod)) &mdash; - Reflection-wrapper for the method
    - '$args ' ('array`) &mdash; - array of arguments
- Returns 'array' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### 'sanitizeSQLPropertyArgs()' <a name= "sanitizeSQLPropertyArgs" ></a>

Check the arguments for the method property

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$property ' (['ReflectionProperty']) (http://php.net/class.ReflectionProperty)) &mdash; - Reflection-wrapper for SQL properties
    - '$args ' ('array`) &mdash; - array of arguments
- Returns 'array' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### 'argAvailabilityCheck (`'<a name= "argAvailabilityCheck" ></a>

Checking for arguments

#### Signature

- **protected static * * method.
- Can take the following parameter (s):
    - '$paramName ' ('string`) &mdash; - parameter name
    - '$args ' ('array`) &mdash; - array of arguments
    - '$optionParams ' ('array`) &mdash; - array of optional parameters
- It doesn't make it back.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### 'getDocParams()' <a name= "getDocParams" ></a>

Return an array of DOCBLOCK parameters and a subgroup of optional parameters

#### Signature

- **protected static * * method.
- Can take the following parameter (s):
    - '$docParams ' ('array`) &mdash; - initial array of parameters
- Returns 'array' value.

### 'docTypeCheck()' <a name= "docTypeCheck" ></a>

Check arguments for compliance with the type

#### Signature

- **protected static * * method.
- Can take the following parameter (s):
    - '$arg ' &mdash; - argument value
    - '$paramName ' ('string`) &mdash; - argument/parameter name
    - '$paramType ' ('string`) &mdash; - required type or group of types
    - '$docBlock ' ('phpDocumentor\Reflection\DocBlock') &mdash; - reference the DOCBLOCK object of a method or property
- It doesn't make it back.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### 'typeCheck()' <a name= "typeCheck" ></a>

Check the values for conformity to type

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$value ' &mdash; - value
    - '$types ' ('array`) &mdash; - accepted types
    - '$denyFuzzy ' ('bool`) &mdash; - whether strict comparison is used
- Returns `bool ' value.

### 'getSQLParams()' <a name= "getSQLParams" ></a>

Get all parameters from SQL query

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$sql`
- Returns 'array' value.


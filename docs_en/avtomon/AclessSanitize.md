<small>avtomon </small>

AclessSanitize
==============

RuntimeCheckerClass

Description
-----------

Class AclessSanitize

Signature
---------

-** class**.

Properties
----------

classsets the followingproperties:

-[`$reflector`](#$reflector) &mdash; Method Reflection or SQLProperties
-[`$args`](#$args) &mdash; Array ofarguments
-[`$sanitizedArgs`](#$sanitizedArgs) &mdash; Array of pointedarguments

###`$reflector`<a name="reflector"></a>

MethodReflection or SQLProperties

#### Signature

-** protected **property.
-Can be one of the followingtypes:
  - [`ReflectionMethod`](http://php.net/class.ReflectionMethod)
  - [`ReflectionProperty`](http://php.net/class.ReflectionProperty)

###`$args`<a name="args"></a>

Arrayofarguments

#### Signature

-** protected **property.
-The value of`array`.

###`$sanitizedArgs`<a name="sanitizedArgs"></a>

Arrayof pointedarguments

#### Signature

-** protected **property.
-Can be one of the followingtypes:
- array
- `null`

Methods
-------

Classmethodsclass:

-[`__construct()`](#__construct) &mdash;Constructor
-[`sanitizeArgs()`](#sanitizeArgs) &mdash; Check and cleararguments
-[`sanitizeMethodArgs()`](#sanitizeMethodArgs) &mdash; Check methodarguments
-[`sanitizeSQLPropertyArgs()`](#sanitizeSQLPropertyArgs) &mdash; Check the arguments for the methodproperty
-[`argAvailabilityCheck()`](#argAvailabilityCheck) &mdash; Checking forarguments
-[`getDocParams()`](#getDocParams) &mdash; Return an array of DOCBLOCK parameters and a subgroup of optionalparameters
-[`docTypeCheck()`](#docTypeCheck) &mdash; Checking the arguments to match thetype
-[`typeCheck()`](#typeCheck) &mdash; Checking the value for thetype
-[`getSQLParams()`](#getSQLParams) &mdash; Get all the parameters from the SQLquery

###`__construct()`<a name="__construct"></a>

Constructor

#### Signature

-** public **method.
-It can take the following parameter(s):
-`$reflector`([`Reflector`](http://php.net/class.Reflector)) &mdash; - method reflection or SQLproperties
-`$args`(`array`) &mdash; - array ofarguments
-Returnsnothing.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)

###`sanitizeArgs()`<a name="sanitizeArgs"></a>

Checkand cleararguments

#### Signature

-** public **method.
Returnsthe `array`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)

###`sanitizeMethodArgs()`<a name="sanitizeMethodArgs"></a>

Checkmethodarguments

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$method`([`ReflectionMethod`](http://php.net/class.ReflectionMethod)) &mdash; - Reflection wrapper formethod
-`$args`(`array`) &mdash; - array ofarguments
Returnsthe `array`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)

###`sanitizeSQLPropertyArgs()`<a name="sanitizeSQLPropertyArgs"></a>

Checkthe arguments for the methodproperty

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$property`([`ReflectionProperty`](http://php.net/class.ReflectionProperty)) &mdash; - Reflection-wrapper forSQL-property
-`$args`(`array`) &mdash; - array ofarguments
Returnsthe `array`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)

###`argAvailabilityCheck()`<a name="argAvailabilityCheck"></a>

Checkingforarguments

#### Signature

-** protected static **method.
-It can take the following parameter(s):
-`$paramName`(`string`) &mdash; - parametername
-`$args`(`array`) &mdash; - array ofarguments
-`$optionParams`(`array`) &mdash; - array of optionalparameters
-Returnsnothing.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)

###`getDocParams()`<a name="getDocParams"></a>

Returnan array of DOCBLOCK parameters and a subgroup of optionalparameters

#### Signature

-** protected static **method.
-It can take the following parameter(s):
-`$docParams`(`array`) &mdash; - source array ofparameters
Returnsthe `array`value.

###`docTypeCheck()`<a name="docTypeCheck"></a>

Checkingthe arguments to match thetype

#### Signature

-** protected static **method.
-It can take the following parameter(s):
-`$arg`&mdash; - the value of theargument
-`$paramName`(`string`) &mdash; - name of theargument/parameter
-`$paramType`(`string`) &mdash; - required type or group oftypes
-`$docBlock`(`phpDocumentor\Reflection\DocBlock`) &mdash; - reference to the DOCBLOCK object of the method orproperty
-Returnsnothing.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)

###`typeCheck()`<a name="typeCheck"></a>

Checkingthe value for thetype

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$value`&mdash; -value
-`$types`(`array`) &mdash; - acceptedtypes
-`$denyFuzzy`(`bool`) &mdash; - Is a strict comparisonused?
-Returns the `bool`value.

###`getSQLParams()`<a name="getSQLParams"></a>

Getall the parameters from the SQLquery

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$sql`
Returnsthe `array`value.


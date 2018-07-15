<small>avtomon </small>

AclessModelResult
=================

Theresult class of themodel

Description
-----------

Class AclessModelResult

Signature
---------

-** class**.
-It is a subclass of the class`avtomon\DbResultItem`.

Properties
----------

classsets the followingproperties:

-[`$class`](#$class) &mdash; Reflection of the modelclass
-[`$method`](#$method) &mdash; Reflection of the modelmethod
-[`$property`](#$property) &mdash; Reflecting the modelproperty
-[`$args`](#$args) &mdash; ExecutionArguments
-[`$isPlainArgs`](#$isPlainArgs) &mdash; true - the model method takes arguments as aset
false- as an associativearray

###`$class`<a name="class"></a>

Reflectionof the modelclass

#### Signature

-** protected **property.
-Can be one of the followingtypes:
- `null`
  - [`ReflectionClass`](http://php.net/class.ReflectionClass)

###`$method`<a name="method"></a>

Reflectionof the modelmethod

#### Signature

-** protected **property.
-Can be one of the followingtypes:
- `null`
  - [`ReflectionMethod`](http://php.net/class.ReflectionMethod)

###`$property`<a name="property"></a>

Reflectingthe modelproperty

#### Signature

-** protected **property.
-Can be one of the followingtypes:
- `null`
  - [`ReflectionProperty`](http://php.net/class.ReflectionProperty)

###`$args`<a name="args"></a>

Execution Arguments

#### Signature

-** protected **property.
-The value of`array`.

###`$isPlainArgs`<a name="isPlainArgs"></a>

true- the model method takes arguments as aset
false- as an associativearray

#### Signature

-** protected **property.
-The value of`bool`.

Methods
-------

Classmethodsclass:

-[`__construct()`](#__construct) &mdash; AclessModelResultconstructor
-[`getClass()`](#getClass) &mdash; Getter for reflecting the modelclass
-[`getMethod()`](#getMethod) &mdash; Getter to reflect the method of themodel
-[`getProperty()`](#getProperty) &mdash; Getter to reflect modelproperties
-[`getArgs()`](#getArgs) &mdash; Getter for executionarguments
-[`getIsPlainArgs()`](#getIsPlainArgs) &mdash; Will the execution parameters be loaded as a sequence ofarguments
-[`setRawResult()`](#setRawResult) &mdash; Add result from another objectDbResultItem
-[`checkDocReturn()`](#checkDocReturn) &mdash; Check the return type for the types specified inDOCBLOCK

###`__construct()`<a name="__construct"></a>

AclessModelResult constructor

#### Signature

-** public **method.
-It can take the following parameter(s):
-`$class`([`ReflectionClass`](http://php.net/class.ReflectionClass)) &mdash; - reflection of the modelclass
-`$method`([`ReflectionMethod`](http://php.net/class.ReflectionMethod)) &mdash; - reflection of the modelmethod
-`$property`([`ReflectionProperty`](http://php.net/class.ReflectionProperty)) &mdash; - reflection of the modelproperty
-`$args`(`array`) &mdash; - executionarguments
-`$isPlainArgs`(`bool`) &mdash; - true - the model method takes arguments as a set, false - as an associativearray
-`$result`(`null`| `mixed`) &mdash;result
-Returnsnothing.

###`getClass()`<a name="getClass"></a>

Getterfor reflecting the modelclass

#### Signature

-** public **method.
-Can return one of the followingvalues:
- `null`
  - [`ReflectionClass`](http://php.net/class.ReflectionClass)

###`getMethod()`<a name="getMethod"></a>

Getterto reflect the method of themodel

#### Signature

-** public **method.
-Can return one of the followingvalues:
- `null`
  - [`ReflectionMethod`](http://php.net/class.ReflectionMethod)

###`getProperty()`<a name="getProperty"></a>

Getterto reflect modelproperties

#### Signature

-** public **method.
-Can return one of the followingvalues:
- `null`
  - [`ReflectionProperty`](http://php.net/class.ReflectionProperty)

###`getArgs()`<a name="getArgs"></a>

Getterfor executionarguments

#### Signature

-** public **method.
-Can return one of the followingvalues:
- array
- `null`

###`getIsPlainArgs()`<a name="getIsPlainArgs"></a>

Willthe execution parameters be loaded as a sequence ofarguments

#### Signature

-** public **method.
-Returns the `bool`value.

###`setRawResult()`<a name="setRawResult"></a>

Addresult from another objectDbResultItem

#### Signature

-** public **method.
-It can take the following parameter(s):
-`$rawResult`(`avtomon\DbResultItem`|`null`)
-Returnsnothing.

###`checkDocReturn()`<a name="checkDocReturn"></a>

Checkthe return type for the types specified inDOCBLOCK

#### Signature

-** public **method.
-Returnsnothing.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)


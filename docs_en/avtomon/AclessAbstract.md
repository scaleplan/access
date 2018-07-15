<small>avtomon </small>

AclessAbstract
==============

Super class

Description
-----------

Class AbstractAcless

Signature
---------

-** abstract class**.

Properties
----------

Theabstract class sets the followingproperties:

-[`$config`](#$config) &mdash;Configuration
-[`$userId`](#$userId) &mdash; UserID
-[`$ps`](#$ps) &mdash; Connecting toRDBMS
-[`$cs`](#$cs) &mdash; Connecting to thecache
-[`$instance`](#$instance) &mdash; Instanceclass

###`$config`<a name="config"></a>

Configuration

#### Signature

-** protected **property.
-The value of`array`.

###`$userId`<a name="userId"></a>

User ID

#### Signature

-** protected **property.
-The value of`int`.

###`$ps`<a name="ps"></a>

ConnectingtoRDBMS

#### Signature

-** protected **property.
-Can be one of the followingtypes:
- `null`
  - [`PDO`](http://php.net/class.PDO)

###`$cs`<a name="cs"></a>

Connectingto thecache

#### Signature

-** protected **property.
-Can be one of the followingtypes:
- `null`
- `Redis`

###`$instance`<a name="instance"></a>

Instance class

#### Signature

**protected static **property.
-Can be one of the followingtypes:
- `null`
-[`AclessAbstract`](../avtomon/AclessAbstract.md)

Methods
-------

Abstractclassmethods:

-[`create()`](#create) &mdash;Singleton
-[`__construct()`](#__construct) &mdash; AclessAbstractconstructor
-[`getUserId()`](#getUserId) &mdash; Returns the userID
-[`getPSConnection()`](#getPSConnection) &mdash; Return connection toRDBMS
-[`getConfig()`](#getConfig) &mdash; Return configuration or part ofit

###`create()`<a name="create"></a>

Singleton

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$userId`(`int`) &mdash; - userID
-`$confPath`(`string`) &mdash; - path to the configurationfile
-Returns [`AclessAbstract`](../ avtomon/AclessAbstract.md)value.

###`__construct()`<a name="__construct"></a>

AclessAbstract constructor

#### Signature

-** private **method.
-It can take the following parameter(s):
-`$userId`(`int`) &mdash; - userID
-`$confPath`(`string`) &mdash; - even to theconfiguration
-Returnsnothing.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)

###`getUserId()`<a name="getUserId"></a>

Returnsthe userID

#### Signature

-** public **method.
Returnsthe intvalue.

###`getPSConnection()`<a name="getPSConnection"></a>

Returnconnection toRDBMS

#### Signature

-** protected **method.
-Returns [`PDO`](http://php.net/class.PDO)value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)

###`getConfig()`<a name="getConfig"></a>

Returnconfiguration or part ofit

#### Signature

-** public **method.
-It can take the following parameter(s):
-`$key`(`string`) &mdash; - configurationkey
-Can return one of the followingvalues:
- array
- `mixed`
- `null`


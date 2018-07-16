<small> avtomon </small>

AclessAbstract
==============

Super class

Description
-----------

Class AbstractAcless

Signature
---------

- **abstract class**.

Properties
----------

The abstract class sets the following properties:

  - [`$config`](#$config) &mdash; Configuration
  - [`$userId`](#$userId) &mdash; User ID
  - [`$ps`](#$ps) &mdash; Connecting to RDBMS
  - [`$cs`](#$cs) &mdash; Connecting to the cache
  - [`$instance`](#$instance) &mdash; Instance class

### `$config`<a name="config"> </a>

Configuration

#### Signature

- **protected** property.
- The value of `array`.

### `$userId`<a name="userId"> </a>

User ID

#### Signature

- **protected** property.
- The value of `int`.

### `$ps`<a name="ps"> </a>

Connecting to RDBMS

#### Signature

- **protected** property.
- Can be one of the following types:
- `null`
  - [`PDO`](http://php.net/class.PDO)

### `$cs`<a name="cs"> </a>

Connecting to the cache

#### Signature

- **protected** property.
- Can be one of the following types:
- `null`
- `Redis`

### `$instance`<a name="instance"> </a>

Instance class

#### Signature

**protected static** property.
- Can be one of the following types:
- `null`
  - [`AclessAbstract`](../ avtomon/AclessAbstract.md)

Methods
-------

Abstract class methods:

  - [`create()`](#create) &mdash; Singleton
  - [`__construct()`](#__construct) &mdash; AclessAbstract constructor
  - [`getUserId()`](#getUserId) &mdash; Returns the user ID
  - [`getPSConnection()`](#getPSConnection) &mdash; Return connection to RDBMS
  - [`getConfig()`](#getConfig) &mdash; Return configuration or part of it

### `create()`<a name="create"> </a>

Singleton

#### Signature

- **public static** method.
- It can take the following parameter (s):
	- `$userId`(`int`) &mdash; - user ID
	- `$confPath`(`string`) &mdash; - path to the configuration file
- Returns [`AclessAbstract`](../ avtomon/AclessAbstract.md) value.

### `__construct()`<a name="__construct"> </a>

AclessAbstract constructor

#### Signature

- **private** method.
- It can take the following parameter (s):
	- `$userId`(`int`) &mdash; - user ID
	- `$confPath`(`string`) &mdash; - even to the configuration
- Returns nothing.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `getUserId()`<a name="getUserId"> </a>

Returns the user ID

#### Signature

- **public** method.
Returns the int value.

### `getPSConnection()`<a name="getPSConnection"> </a>

Return connection to RDBMS

#### Signature

- **protected** method.
- Returns [`PDO`](http://php.net/class.PDO) value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `getConfig()`<a name="getConfig"> </a>

Return configuration or part of it

#### Signature

- **public** method.
- It can take the following parameter (s):
	- `$key`(`string`) &mdash; - configuration key
- Can return one of the following values:
- array
- `mixed`
- `null`


the <small>avtomon</small>

AclessAbstract
==============

Superclass

Description
-----------

Class Abstracless

Signature
---------

- **abstract class**.

Properties
----------

the abstract class sets the following properties:

- ['$config'](#$config) &mdash; Configuration
- ['$userId'](#$userId) &mdash; user ID
- ['$ps'](#$ps) &mdash; connection to RDBMS
- ['$cs'](#$cs) &mdash; connect to cache
- ['$instance'](#$instance) &mdash; class Instance

### '$config '<a name= "config" ></a>

Configuration

#### Signature

- **protected * * property.
- Value 'array'.

### '$userId '<a name= "userId" ></a>

user ID

#### Signature

- **protected * * property.
- The value `int`.

### '$ps '<a name= " ps " ></a>

Connecting to RDBMS

#### Signature

- **protected * * property.
- Can be one of the following types:
    - 'null`
    - ['PDO'](http://php.net/class.PDO)

### '$cs '<a name= " cs " ></a>

Connect to cache

#### Signature

- **protected * * property.
- Can be one of the following types:
    - 'null`
    - 'Redis`

### '$instance '<a name= "instance" ></a>

An instance of the class

#### Signature

- **protected static * * property.
- Can be one of the following types:
    - 'null`
    - ['AclessAbstract'](../avtomon/AclessAbstract.md)

Methods
-------

Methods of the abstract class:

- ['create()'](#create) &mdash; singleton
  - [`__construct (`' ](#__construct) &mdash; AclessAbstract constructor
  - [`getUserId()`](#getUserId) &mdash; Returns the ID of the user
- ['getPSConnection()'](#getPSConnection) &mdash; Return connection to RDBMS
- ['getConfig (`' ](#getConfig) &mdash; Return the configuration or part of it

### 'create()' <a name= 'create' ></a>

Singleton

#### Signature

- **public static * * method.
- Can take the following parameter (s):
    - '$userId ' ('int`) &mdash; - user ID
    - '$confPath ' ('string`) &mdash; - path to configuration file
- Return [`AclessAbstract`](../avtomon/AclessAbstract.md) value.

### `__construct() '<a name= "__construct " ></a>

AclessAbstract constructor

#### Signature

- **private * * method.
- Can take the following parameter (s):
    - '$userId ' ('int`) &mdash; - user ID
    - '$confPath ' ('string`) &mdash; - let to configuration
- It doesn't make it back.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### 'getUserId()' <a name= 'getUserId' ></a>

Returns the user ID

#### Signature

- **public * * method.
- Returns ' int ' value.

### 'getPSConnection()' <a name= "getPSConnection" ></a>

To return the connection to the RDBMS

#### Signature

- **protected * * method.
- Returns ['PDO'](http://php.net/class.PDO) value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### 'getConfig()' <a name= "getConfig" ></a>

Return the configuration or part of it

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$key ' ('string`) &mdash; - configuration key
- Can return one of the following values:
    - 'array`
    - 'mixed`
    - 'null`


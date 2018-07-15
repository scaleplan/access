the <small>avtomon</small>

AclessModify
============

Class of changes

Description
-----------

Class AclessModify

Signature
---------

- **class**.
- Is a subclass of the class ['AclessAbstract'](../avtomon/AclessAbstract.md).

Properties
----------

class sets the following properties:

- ['$instance'](#$instance) &mdash; class Instance

### '$instance '<a name= "instance" ></a>

An instance of the class

#### Signature

- **protected static * * property.
- Can be one of the following types:
    - 'null`
    - ['AclessModify'](../avtomon/AclessModify.md)

Methods
-------

Class methods class:

  - [`loadAclessRights()`](#loadAclessRights) &mdash; Retrieve access rights for the current user in the cache
  - [`initSQLScheme()'](#initSQLScheme) &mdash; Fill in the database schema to work with Acless
- ['initPersistentStorage()'](#initPersistentStorage) &mdash; Initialize persistent storage of access rights data
- ['addRoleAccessRight()'](#addRoleAccessRight) &mdash; check/change the default permissions for the role
- ['addUserToRole()'](#addUserToRole) &mdash; Give role to user
- ['addAccessRight()'](#addAccessRight) &mdash; Add/change dotup's right
  - [`shiftAccessRightFromRole()`](#shiftAccessRightFromRole) &mdash; to Create a right of access for the user based on the rights for his role

### 'loadAclessRights()' <a name= "loadAclessRights" ></a>

Load permissions for the current user to the cache

#### Signature

- **public * * method.
- It doesn't make it back.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - `avtomon\RedisSingletonException`

### `initSQLScheme() '<a name= "initSQLScheme" ></a>

Fill in the database scheme to work with Acless

#### Signature

- **protected * * method.
- Returns ' int ' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### `initPersistentStorage() '<a name= "initPersistentStorage" ></a>

Inicializirati to the persistent data store access rights

#### Signature

- **public * * method.
- Returns ' int ' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### 'addRoleAccessRight()' <a name= "addRoleAccessRight" ></a>

set/change default permissions for the role

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$url_id ' ('int`) &mdash; - url identifier
    - '$role ' ('string`) &mdash; - role name
- Returns 'array' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### 'addUserToRole()' <a name= "addUserToRole" ></a>

To give a role to a user

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$user_id ' ('int`) &mdash; - user ID
    - '$role ' ('string`) &mdash; - role name
- Returns 'array' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### 'addAccessRight()' <a name= "addAccessRight" ></a>

Add/change dotup's right

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$url_id ' ('int`) &mdash; - url identifier
    - '$user_id ' ('int`) &mdash; - user ID
    - '$is_allow ' ('bool`) &mdash; - $values will be allow or deny
    - '$values ' ('array`) &mdash; - which filter values to allow/deny access to
- Returns 'array' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### `shiftAccessRightFromRole (`'<a name= "shiftAccessRightFromRole" ></a>

Create an access right for a user based on the rights for its role

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$userId ' ('int`) &mdash; - user ID
- Returns 'array' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)


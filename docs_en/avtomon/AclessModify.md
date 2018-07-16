<small> avtomon </small>

AclessModify
============

Class of change

Description
-----------

Class AclessModify

Signature
---------

- **class**.
- It is a subclass of the class [`AclessAbstract`](../ avtomon/AclessAbstract.md).

Properties
----------

class sets the following properties:

  - [`$instance`](#$instance) &mdash; Instance class

### `$instance`<a name="instance"> </a>

Instance class

#### Signature

**protected static** property.
- Can be one of the following types:
- `null`
  - [`AclessModify`](../ avtomon/AclessModify.md)

Methods
-------

Class methods class:

  - [`loadAclessRights()`](#loadAclessRights) &mdash; Load the current user's access rights into the cache
- [initSQLScheme() `](#initSQLScheme) &mdash; Fill in the database the scheme for working with Acless
  - [`initPersistentStorage()`](#initPersistentStorage) &mdash; Initiate a persistent storage of access rights data
  - [`addRoleAccessRight()`](#addRoleAccessRight) &mdash; Add/Change the default permissions for the role
  - [`addUserToRole()`](#addUserToRole) &mdash; Give the role to the user
  - [`addAccessRight()`](#addAccessRight) &mdash; Add/change right of grant
  - [`shiftAccessRightFromRole()`](#shiftAccessRightFromRole) &mdash; Create an access right for a rights-based user for its role

### `loadAclessRights()`<a name="loadAclessRights"> </a>

Load the current user's access rights into the cache

#### Signature

- **public** method.
- Returns nothing.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)
- `avtomon\RedisSingletonException`

### `initSQLScheme()`<a name="initSQLScheme"> </a>

Fill in the database the scheme for working with Acless

#### Signature

- **protected** method.
Returns the int value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `initPersistentStorage()`<a name="initPersistentStorage"> </a>

Initiate a persistent storage of access rights data

#### Signature

- **public** method.
Returns the int value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `addRoleAccessRight()`<a name="addRoleAccessRight"> </a>

Add/Change the default permissions for the role

#### Signature

- **public** method.
- It can take the following parameter (s):
	- `$url_id`(`int`) &mdash; - identifier URL
	- `$role`(`string`) &mdash; - name of the role
Returns the `array`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `addUserToRole()`<a name="addUserToRole"> </a>

Give the role to the user

#### Signature

- **public** method.
- It can take the following parameter (s):
	- `$user_id`(`int`) &mdash; - user ID
	- `$role`(`string`) &mdash; - name of the role
Returns the `array`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `addAccessRight()`<a name="addAccessRight"> </a>

Add/change right of grant

#### Signature

- **public** method.
- It can take the following parameter (s):
	- `$url_id`(`int`) &mdash; - identifier URL
	- `$user_id`(`int`) &mdash; - user ID
	- `$is_allow`(`bool`) &mdash; - $values ​​will be permissive or prohibitive
	- `$values`(`array`) &mdash; - with what filter values ​​to allow/deny access
Returns the `array`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)

### `shiftAccessRightFromRole()`<a name="shiftAccessRightFromRole"> </a>

Create an access right for a rights-based user for its role

#### Signature

- **public** method.
- It can take the following parameter (s):
	- `$userId`(`int`) &mdash; - user ID
Returns the `array`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../ avtomon/AclessException.md)


<small> avtomon </small>

Acless
======

Class of formation of the list of URLs and verification of rights

Description
-----------

Class Acless

Signature
---------

- **class**.
- It is a subclass of the class [`AclessAbstract`](../avtomon/AclessAbstract.md).

Constants
---------

class sets the following constants:

  - [`ACLESS_403_ERROR_CODE`](#ACLESS_403_ERROR_CODE) &mdash; Acless error code indicating private access to the resource
  - [`ACLESS_UNAUTH_ERROR_CODE`](#ACLESS_UNAUTH_ERROR_CODE) &mdash; Acless error code indicating unresolved unauthorized request

Properties
----------

class sets the following properties:

  - [`$instance`](#$instance) &mdash; Instance class
  - [`$filterSeparator`](#$filterSeparator) &mdash; Separator of filter values

### `$instance`<a name="instance"> </a>

Instance class

#### Signature

**protected static** property.
- Can be one of the following types:
  - `null`
  - [`Acless`](../avtomon/Acless.md)

### `$filterSeparator`<a name="filterSeparator"> </a>

Separator of filter values

#### Signature

- **protected** property.
- The value of `string`.

Methods
-------

Class methods class:

  - [`getAccessRights()`](#getAccessRights) &mdash; Return information about all available URLs or about a specific URL
  - [`checkMethodRights()`](#checkMethodRights) &mdash; Check access to the method
  - [`checkFileRights()`](#checkFileRights) &mdash; Check access to the file
  - [`methodToURL()`](#methodToURL) &mdash; Generate URL from controller name and method name
  - [`generateControllerURLs()`](#generateControllerURLs) &mdash; Generate array of controllers' URLs
  - [`getRecursivePaths()`](#getRecursivePaths) &mdash; Find all files in the directory, including subdirectories
  - [`getControllerURLs()`](#getControllerURLs) &mdash; Returns all controller controls
  - [`getFilesURLs()`](#getFilesURLs) &mdash; Returns all file paths
  - [`getPlainURLs()`](#getPlainURLs) &mdash; Returns the URLs specified in the configuration file
  - [`getAllURLs()`](#getAllURLs) &mdash; Returns all collected URLs

### `getAccessRights()`<a name="getAccessRights"> </a>

Return information about all available URLs or about a specific URL

#### Signature

- **public** method.
- It can take the following parameter (s):
  - `$url`(`string`) - text Urla
Returns the `array`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)
  - `avtomon\RedisSingletonException`

### `checkMethodRights()`<a name="checkMethodRights"> </a>

Check access to the method

#### Signature

- **public** method.
- It can take the following parameter (s):
  - `$refMethod`([`Reflector`](http://php.net/class.Reflector)) - Reflection-wrapper for the method
  - `$args`(`array`) - options for running
  - `$refClass`([`ReflectionClass`](http://php.net/class.ReflectionClass)) - method, class
- Returns the `bool`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)
  - `avtomon\RedisSingletonException`

### `checkFileRights()`<a name="checkFileRights"> </a>

Check access to the file

#### Signature

- **public** method.
- It can take the following parameter (s):
  - `$filePath`(`string`) - path to the file
- Returns the `bool`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)
  - `avtomon\RedisSingletonException`

### `methodToURL()`<a name="methodToURL"> </a>

Generate URL from controller name and method name

#### Signature

- **public** method.
- It can take the following parameter (s):
  - `$className`(`string`) - class name
  - `$methodName`(`string`) - method name
Returns `string`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)

### `generateControllerURLs()`<a name="generateControllerURLs"> </a>

Generate array of controllers' URLs

#### Signature

- **protected** method.
- It can take the following parameter (s):
  - `$controllerFileName`(`string`) - controller file name
  - `$controllerNamespace`(`string`) - namespace for the controller, if any
Returns the `array`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)
  - [`ReflectionException`](http://php.net/class.ReflectionException)

### `getRecursivePaths()`<a name="getRecursivePaths"> </a>

Find all files in the directory, including subdirectories

#### Signature

- **protected** method.
- It can take the following parameter (s):
  - `$dir`(`string`) - directory path
Returns the `array`value.

### `getControllerURLs()`<a name="getControllerURLs"> </a>

Returns all controller controls

#### Signature

- **public** method.
Returns the `array`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)
  - [`ReflectionException`](http://php.net/class.ReflectionException)

### `getFilesURLs()`<a name="getFilesURLs"> </a>

Returns all file paths

#### Signature

- **public** method.
Returns the `array`value.

### `getPlainURLs()`<a name="getPlainURLs"> </a>

Returns the URLs specified in the configuration file

#### Signature

- **public** method.
Returns the `array`value.

### `getAllURLs()`<a name="getAllURLs"> </a>

Returns all collected URLs

#### Signature

- **public** method.
Returns the `array`value.
- Throws one of the following exceptions:
  - [`avtomon\AclessException`](../avtomon/AclessException.md)
  - [`ReflectionException`](http://php.net/class.ReflectionException)


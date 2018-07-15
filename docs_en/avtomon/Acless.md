the <small>avtomon</small>

Acless
======

The class generate a list of URLs and check the rights

Description
-----------

Class Acless

Signature
---------

- **class**.
- Is a subclass of the class ['AclessAbstract'](../avtomon/AclessAbstract.md).

Constants
---------

class sets the following constants:

- ['ACLESS_403_ERROR_CODE'](#ACLESS_403_ERROR_CODE) &mdash; an Acless error Code indicating private access to a resource
- ['ACLESS_UNAUTH_ERROR_CODE'](#ACLESS_UNAUTH_ERROR_CODE) &mdash; an Acless error Code indicating an unauthorized unauthorized request

Properties
----------

class sets the following properties:

- ['$instance'](#$instance) &mdash; class Instance
- ['$filterSeparator'](#$filterSeparator) &mdash; filter value Separator

### '$instance '<a name= "instance" ></a>

An instance of the class

#### Signature

- **protected static * * property.
- Can be one of the following types:
    - 'null`
    - ['Acless'](../avtomon/Acless.md)

### '$filterSeparator '<a name= "filterSeparator" ></a>

Filter value separator

#### Signature

- **protected * * property.
- Value `string'.

Methods
-------

Class methods class:

  - [`getAccessRights()`](#getAccessRights) &mdash; Return information about all user-accessible URLs or some specific url
- ['checkMethodRights()'](#checkMethodRights) &mdash; Check access to method
- ['checkFileRights()'](#checkFileRights) &mdash; Check file access
- ['methodToURL()'](#methodToURL) &mdash; Generate url from controller name and method name
- ['generateControllerURLs()'](#generateControllerURLs) &mdash; Generate array of controller URLs
- ['getRecursivePaths()'](#getRecursivePaths) &mdash; Find all files in a directory, including subdirectories
  - [`getControllerURLs()`](#getControllerURLs) &mdash; Returns all of the URLs of controllers
- ['getFilesURLs()'](#getFilesURLs) &mdash; Returns all file URLs
- ['getPlainURLs()'](#getPlainURLs) &mdash; Returns URLs directly specified in the configuration file
- ['getAllURLs()'](#getAllURLs) &mdash; Returns all collected URLs

### 'getAccessRights()' <a name= "getAccessRights" ></a>

Return information about all URLs available to the user or about a particular url

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$url ' ('string`) &mdash; - url text
- Returns 'array' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - `avtomon\RedisSingletonException`

### `checkMethodRights() '<a name= "checkMethodRights" ></a>

Check access to method

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$refMethod ' (['Reflector`]) (http://php.net/class.Reflector)) &mdash; - Reflection-wrapper for the method
    - '$args ' ('array') &mdash; - execution parameters
    - '$refClass ' (['ReflectionClass']) (http://php.net/class.ReflectionClass)) &mdash; - method class
- Returns `bool ' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - `avtomon\RedisSingletonException`

### `checkFileRights() '<a name= "checkFileRights" ></a>

Check file access

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$filePath ' ('string`) &mdash; - file path
- Returns `bool ' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - `avtomon\RedisSingletonException`

### `methodToURL() '<a name= 'methodToURL' ></a>

Generate url from controller name and method name

#### Signature

- **public * * method.
- Can take the following parameter (s):
    - '$className ' ('string`) &mdash; - class name
    - '$methodName ' ('string`) &mdash; - method name
- Returns 'string' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)

### 'generateControllerURLs (`'<a name= "generateControllerURLs" ></a>

Generate an array of URLs controllers

#### Signature

- **protected * * method.
- Can take the following parameter (s):
    - '$controllerFileName ' ('string') &mdash; - controller file name
    	- `$controllerNamespace`(`string`) &mdash; - the namespace for the controller, if there is
- Returns 'array' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - ['ReflectionException'](http://php.net/class.ReflectionException)

### 'getRecursivePaths`)' <a name= "getRecursivePaths" ></a>

Find all files in a directory, including subdirectories

#### Signature

- **protected * * method.
- Can take the following parameter (s):
    - '$dir ' ('string`) &mdash; - directory path
- Returns 'array' value.

### 'getControllerURLs()' <a name= 'getControllerURLs' ></a>

Returns all controller URLs

#### Signature

- **public * * method.
- Returns 'array' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - ['ReflectionException'](http://php.net/class.ReflectionException)

### 'getFilesURLs()' <a name= "getFilesURLs" ></a>

Returns all the URLs of files

#### Signature

- **public * * method.
- Returns 'array' value.

### 'getPlainURLs()' <a name= "getPlainURLs" ></a>

Returns the URLs directly specified in the configuration file

#### Signature

- **public * * method.
- Returns 'array' value.

### 'getAllURLs()' <a name= "getAllURLs" ></a>

Returns all collected URLs

#### Signature

- **public * * method.
- Returns 'array' value.
- Throws one of the following exceptions:
      - [`avtomon\AclessException'](../avtomon/AclessException.md)
    - ['ReflectionException'](http://php.net/class.ReflectionException)


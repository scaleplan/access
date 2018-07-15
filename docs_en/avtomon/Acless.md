<small>avtomon </small>

Acless
======

Classof formation of the list of URLs and verification ofrights

Description
-----------

Class Acless

Signature
---------

-** class**.
-It is a subclass of the class [`AclessAbstract`](../avtomon/AclessAbstract.md).

Constants
---------

classsets the followingconstants:

-[`ACLESS_403_ERROR_CODE`](#ACLESS_403_ERROR_CODE) &mdash; Acless error code indicating private access to theresource
-[`ACLESS_UNAUTH_ERROR_CODE`](#ACLESS_UNAUTH_ERROR_CODE) &mdash; Acless error code indicating unresolved unauthorizedrequest

Properties
----------

classsets the followingproperties:

-[`$instance`](#$instance) &mdash; Instanceclass
-[`$filterSeparator`](#$filterSeparator) &mdash; Separator of filtervalues

###`$instance`<a name="instance"></a>

Instance class

#### Signature

**protected static **property.
-Can be one of the followingtypes:
- `null`
-[`Acless`](../avtomon/Acless.md)

###`$filterSeparator`<a name="filterSeparator"></a>

Separatorof filtervalues

#### Signature

-** protected **property.
-The value of`string`.

Methods
-------

Classmethodsclass:

-[`getAccessRights()`](#getAccessRights) &mdash; Return information about all available URLs or about a specificURL
-[`checkMethodRights()`](#checkMethodRights) &mdash; Check access to themethod
-[`checkFileRights()`](#checkFileRights) &mdash; Check access to thefile
-[`methodToURL()`](#methodToURL) &mdash; Generate URL from controller name and methodname
-[`generateControllerURLs()`](#generateControllerURLs) &mdash; Generate array of controllers'URLs
-[`getRecursivePaths()`](#getRecursivePaths) &mdash; Find all files in the directory, includingsubdirectories
-[`getControllerURLs()`](#getControllerURLs) &mdash; Returns all controllercontrols
-[`getFilesURLs()`](#getFilesURLs) &mdash; Returns all filepaths
-[`getPlainURLs()`](#getPlainURLs) &mdash; Returns the URLs specified in the configurationfile
-[`getAllURLs()`](#getAllURLs) &mdash; Returns all collectedURLs

###`getAccessRights()`<a name="getAccessRights"></a>

Returninformation about all available URLs or about a specificURL

#### Signature

-** public **method.
-It can take the following parameter(s):
-`$url`(`string`) &mdash; - texturl
Returnsthe `array`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)
- `avtomon\RedisSingletonException`

###`checkMethodRights()`<a name="checkMethodRights"></a>

Checkaccess to themethod

#### Signature

-** public **method.
-It can take the following parameter(s):
-`$refMethod`([`Reflector`](http://php.net/class.Reflector)) &mdash; - Reflection wrapper formethod
-`$args`(`array`) &mdash; - executionparameters
-`$refClass`([`ReflectionClass`](http://php.net/class.ReflectionClass)) &mdash; - methodclass
-Returns the `bool`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)
- `avtomon\RedisSingletonException`

###`checkFileRights()`<a name="checkFileRights"></a>

Checkaccess to thefile

#### Signature

-** public **method.
-It can take the following parameter(s):
-`$filePath`(`string`) &mdash; - the path to thefile
-Returns the `bool`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)
- `avtomon\RedisSingletonException`

###`methodToURL()`<a name="methodToURL"></a>

GenerateURL from controller name and methodname

#### Signature

-** public **method.
-It can take the following parameter(s):
-`$className`(`string`) &mdash; - classname
-`$methodName`(`string`) &mdash; - methodname
Returns`string`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)

###`generateControllerURLs()`<a name="generateControllerURLs"></a>

Generatearray of controllers'URLs

#### Signature

-** protected **method.
-It can take the following parameter(s):
-`$controllerFileName`(`string`) &mdash; - controller filename
-`$controllerNamespace`(`string`) &mdash; - namespace for the controller, ifany
Returnsthe `array`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)
  - [`ReflectionException`](http://php.net/class.ReflectionException)

###`getRecursivePaths()`<a name="getRecursivePaths"></a>

Findall files in the directory, includingsubdirectories

#### Signature

-** protected **method.
-It can take the following parameter(s):
-`$dir`(`string`) &mdash; - path to thecatalog
Returnsthe `array`value.

###`getControllerURLs()`<a name="getControllerURLs"></a>

Returnsall controllercontrols

#### Signature

-** public **method.
Returnsthe `array`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)
  - [`ReflectionException`](http://php.net/class.ReflectionException)

###`getFilesURLs()`<a name="getFilesURLs"></a>

Returnsall filepaths

#### Signature

-** public **method.
Returnsthe `array`value.

###`getPlainURLs()`<a name="getPlainURLs"></a>

Returnsthe URLs specified in the configurationfile

#### Signature

-** public **method.
Returnsthe `array`value.

###`getAllURLs()`<a name="getAllURLs"></a>

Returnsall collectedURLs

#### Signature

-** public **method.
Returnsthe `array`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)
  - [`ReflectionException`](http://php.net/class.ReflectionException)


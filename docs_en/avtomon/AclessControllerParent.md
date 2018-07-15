<small>avtomon </small>

AclessControllerParent
======================

Parentfor controllers - checking access rights, filteringparameters

Description
-----------

Class AclessControllerParent

Signature
---------

-** abstract class**.

Properties
----------

Theabstract class sets the followingproperties:

-[`$before`](#$before) &mdash; Functions to execute before executing the controllermethod
-[`$beforeDefaultResult`](#$beforeDefaultResult) &mdash; The result of the default beforefunction
-[`$after`](#$after) &mdash; Functions for execution after execution of the controllermethod
-[`$afterDefaultResult`](#$afterDefaultResult) &mdash; Result of performing the after-function bydefault

###`$before`<a name="before"></a>

Functionsto execute before executing the controllermethod

#### Signature

**protected static **property.
-The value of`array`.

###`$beforeDefaultResult`<a name="beforeDefaultResult"></a>

Theresult of the default beforefunction

#### Signature

-** public static **property.
-The value of`mixed`.

###`$after`<a name="after"></a>

Functionsfor execution after execution of the controllermethod

#### Signature

**protected static **property.
-The value of`array`.

###`$afterDefaultResult`<a name="afterDefaultResult"></a>

Resultof performing the after-function bydefault

#### Signature

-** public static **property.
-The value of`mixed`.

Methods
-------

Abstractclassmethods:

-[`pushBefore()`](#pushBefore) &mdash; Add a function to the end of the array of functions performed before the execution of the controllermethod
-[`unshiftBefore()`](#unshiftBefore) &mdash; Add a function to the beginning of the array of functions performed before the execution of the controllermethod
-[`insertBefore()`](#insertBefore) &mdash; Add a function to the specified position of the array of functions performed before the execution of the controllermethod
-[`pushAfter()`](#pushAfter) &mdash; Add a function to the end of an array of functions executed after the execution of the controllermethod
-[`unshiftAfter()`](#unshiftAfter) &mdash; Add a function to the beginning of an array of functions executed after the execution of the controllermethod
-[`insertAfter()`](#insertAfter) &mdash; Add the function to the specified position of the array of functions executed after the execution of the controllermethod
-[`removeBefore()`](#removeBefore) &mdash; Delete a function or all functions that must be performed before executing the controllermethod
-[`removeAfter()`](#removeAfter) &mdash; Delete a function or all functions that must be performed after the execution of the controllermethod
-[`checkControllerMethod()`](#checkControllerMethod) &mdash; Verifying access rights and input for themethod
-[`executeBeforeHandlers()`](#executeBeforeHandlers) &mdash; Execute handlers for starting the queryexecution
-[`executeAfterHandlers()`](#executeAfterHandlers) &mdash; Execute output handlers for the execution of therequest
-[`__callStatic()`](#__callStatic) &mdash; Validating access rights and input data for staticmethods
-[`__call()`](#__call) &mdash; Validating access rights and input data for non-staticmethods

###`pushBefore()`<a name="pushBefore"></a>

Adda function to the end of the array of functions performed before the execution of the controllermethod

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$function`(`callable`) &mdash; -function
-Returnsnothing.

###`unshiftBefore()`<a name="unshiftBefore"></a>

Adda function to the beginning of the array of functions performed before the execution of the controllermethod

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$function`(`callable`) &mdash; -function
-Returnsnothing.

###`insertBefore()`<a name="insertBefore"></a>

Adda function to the specified position of the array of functions performed before the execution of the controllermethod

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$index`(`int`) &mdash; - Insertionposition
-`$function`(`callable`) &mdash; -function
-Returnsnothing.

###`pushAfter()`<a name="pushAfter"></a>

Adda function to the end of an array of functions executed after the execution of the controllermethod

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$function`(`callable`) &mdash; -function
-Returnsnothing.

###`unshiftAfter()`<a name="unshiftAfter"></a>

Adda function to the beginning of an array of functions executed after the execution of the controllermethod

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$function`(`callable`) &mdash; -function
-Returnsnothing.

###`insertAfter()`<a name="insertAfter"></a>

Addthe function to the specified position of the array of functions executed after the execution of the controllermethod

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$index`(`int`) &mdash; - Insertionposition
-`$function`(`callable`) &mdash; -function
-Returnsnothing.

###`removeBefore()`<a name="removeBefore"></a>

Deletea function or all functions that must be performed before executing the controllermethod

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$index`(`int`) &mdash; - deleteposition
-Returnsnothing.

###`removeAfter()`<a name="removeAfter"></a>

Deletea function or all functions that must be performed after the execution of the controllermethod

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$index`(`int`) &mdash; - deleteposition
-Returnsnothing.

###`checkControllerMethod()`<a name="checkControllerMethod"></a>

Verifyingaccess rights and input for themethod

#### Signature

-** protected static **method.
-It can take the following parameter(s):
-`$methodName`(`string`) &mdash; - methodname
-`$args`(`array`) &mdash; - executionarguments
-`$obj`(`object`) &mdash; - an object to the context of which the method should be executed (if notstatic)
-Returns `avtomon\AbstractResult`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)
- `avtomon\DbResultItemException`
  - [`ReflectionException`](http://php.net/class.ReflectionException)

###`executeBeforeHandlers()`<a name="executeBeforeHandlers"></a>

Executehandlers for starting the queryexecution

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$method`([`ReflectionMethod`](http://php.net/class.ReflectionMethod) | `null`) &mdash; - reflection of the method to beperformed
-`$args`(`array`) &mdash; - hisarguments
Returnsthe `mixed`value.

###`executeAfterHandlers()`<a name="executeAfterHandlers"></a>

Executeoutput handlers for the execution of therequest

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$method`([`ReflectionMethod`](http://php.net/class.ReflectionMethod)) &mdash; - Reflection of the executed method of theConstellor
-`$args`(`array`) &mdash; - hisarguments
-`$result`(`null`) &mdash; - result ofperformance
Returnsthe `mixed`value.

###`__callStatic()`<a name="__callStatic"></a>

Validatingaccess rights and input data for staticmethods

#### Signature

-** public static **method.
-It can take the following parameter(s):
-`$methodName`(`string`) &mdash; - method name or SQLproperties
-`$args`(`array`) &mdash; - array ofarguments
-Returns `avtomon\AbstractResult`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)
- `avtomon\DbResultItemException`
  - [`ReflectionException`](http://php.net/class.ReflectionException)

###`__call()`<a name="__call"></a>

Validatingaccess rights and input data for non-staticmethods

#### Signature

-** public **method.
-It can take the following parameter(s):
-`$methodName`(`string`) &mdash; - method name or SQLproperties
-`$args`(`array`) &mdash; - array ofarguments
-Returns `avtomon\AbstractResult`value.
-Throws one of the followingexceptions:
-[`avtomon\AclessException`](../avtomon/AclessException.md)
- `avtomon\DbResultItemException`
  - [`ReflectionException`](http://php.net/class.ReflectionException)


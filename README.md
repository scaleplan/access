# Acless

Thesystem of access rights management + checking the types ofarguments.

### Installation

``
composerreqireavtomon/acless
``
<br>
### Initialization

```
cd vendor/avtomon/Acless

./initschemadata
```

whereschema and data are optional parameters indicating the need to generate the Acless schema in the database and APIs, the files and entered in the configurationrespectively.

<br>

###Mechanics ofoperation

Thecontroller method is called from outside the controller class. How this happens does notmatter.

Ifthe method is public, or in the comment, the method's value is specified in the method * acless_no_rights_check*
configuration,the system is not enabled and execution occurs asusual.
Ifthe method is private (access modifiers private and protected) and if a special phpdoc-tag of the method's processing is specified by the system Acless (the value of the acless_label configuration directive), then a query is made to the database, which checks whether the method can execute with the parameters arrived and for a specific user (user ID is set when creating Aclessobjects).

For example:

```
class User
{
/ **
*Get userobject
*
* @aclessMethod
*
*@param int $id - useridentifier
*
*@returnUserAbstract
* /
protectedstatic function get (int $id):UserAbstract
{
// ...
}
}
```

Inthis example, the current user's access to the static get method of the User class will be checked for any values ​​of the $idargument.

However,you can define access to execute a method with certainarguments:

```
/ **
*Get userobject
*
* @aclessMethod
*
*@aclessFilterid
*
*@param int $id - useridentifier
*
*@returnUserAbstract
* /
protectedstatic function actionGet (int $id):UserAbstract
{
// ...
}
```

Inthis example, access is allowed only if the value of the filter argument $id is included in the list of allowed values ​​stored in the database (the * values ​​* column of the table * access_right *). A list in the database will looklike:

``
ARRAY['<filter value 1>, <filter value 2',...]
``

Youcan filter by severalarguments:

```
/ **
*Set userrole
*
* @aclessMethod
*
*@aclessFilter id,role
*
*@param int $id - useridentifier
*@param string $role - userrole
*
*@returnvoid
* /
protectedstatic function actionSetRole (int $id, string $role):void
{
// ...
}
```

Inthis case, the list of allowed occurrences will have theformat:

```
ARRAY['<value for the first filter> <separator> <value for the second filter> ...',...]
```

Thus,in order to allow the execution of the method ```User :: setRole (21, 'Moderator')```it is necessary that the list of allowed values ​​be set to `21: Moderator`, for the default splitter <b>: </b>

Themodule supports checking the types of input parameters. Php 7 supports type hinting for type checking, however, Acless acts moreintelligently:

1.In PHP, the method arguments and the return type can have only onetype:

``
protectedstatic function setRole (int $id, string $role):void
``

Ifwe want to type with several types, such as in C # orTypeScript:

```
setMultiData(object: HTMLElement | NodeList, data: Object | Object [] | string = this.data): HTMLElement |NodeList

```

thennative PHP will not allow you to dothis.

TheAcless type checking subsystem can target * PHPDOC * and check the values ​​for matching to several types if they are specified in * PHPDOC*:

```
/ **
*Set userrole
*
* @aclessMethod
*
*@aclessFilter id,role
*
*@param int | string $id - useridentifier
*@param string | IRole $role - userrole
*
*@return UserAbstract |void
* /
protectedstatic function actionSetRole (int $id, string $role)
{
// ...
}
```

2.By default, the value of the argument can be considered "correct", even if its type does not match the expected one, but the value returned to the expected type (or to one of the expected ones) does not differ from the original in the case of fuzzy comparison (==). This behavior can be turned off by setting a tag from the * deny_fuzzy * configuration directive for themethod.

Thisfunctionality is available. As for the methods of controllers, this is for the methods ofmodels.
<br>

Themodule supports the generation of URLs to API methods from controllerfiles.

Todo this, you only need to specify the necessary configuration directives in the Acless configurationfile.

```
controllers:
- path:/var/www/project/controllers
method_prefix: action
namespace: app\controllers
```

Aftergeneration, the table * acless.url*
<br>

Inthe configuration file, you can specify the roles of the users of thesystem

```
roles:
- Administrator
- Moderator
- Listener
-Aguest
```
Whythese roles can be linked to existing users registered in the system and set the default access rights for eachrole.

Inspite of this, further access rights for any user can be changed irrespective of the initial set of rights - access rights by default exist only to specify it was possible to automatically give out a set of rights to theuser.

Themodule supports the management of access rights for private files. The mechanism of operation is the same as for the API. In fact, the system still works with URLs for methods of controllers or with corners of private files. To generate links to files, you only need to specify in the config directory where these files arestored:

```
files:
-/var/www/project/view/private/materials
```

AdditionalURLs for verification can be specified simply by writing them to the configuration file in the urlsdirective:

```
urls:
- /var/www/project/file.jpg
-/var/www/project/get-a-lot-of-money
```

<br>

Towork correctly with the methods of controllers, it is necessary that the classes of controllers to be processed are inherited from the class AclessControllerParent. To test the arguments of the model methods, you need to inherit the classes of models from the classAclessModelParent.
<br>

Themain data store of the system is PostgreSQL. However, the data needed to verify access rights is cached in the Redis repository. To increaseproductivity.

Whendata is changed in the main repository (PostgreSQL), the data in the cache (Redis) is automatically updated by the trigger. In order for the trigger to be executed correctly by the user of the process, PostgreSQL must have access to the Redisrepository.

<br>

####AdditionalFeatures:

1.Supports the addition of collbacks that run before and after the successful execution of the controller method. In this case, these bulbs can change the input data and the resulting result,respectively.

2.During initialization, the module downloads the name of all the database tables from the database. In the future, access to these tables will be handled by the rights checking subsystem. You can either edit this information in thedatabase.

3.In addition to the database, you can specify the type of controller method, the same type of API methods to know which method will be modifying, deleting, creating or reading, it can be convenient for filtering the methods of controllers in the userinterface.

<br>

[Documentation](docs_en)

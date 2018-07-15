# Acless

Access control system + check of argument types.

### Installation

`
composer reqire avtomon/acless
`
<br>
### Initialization

```
cd vendor/avtomon/Acless

./init schema data
```

where schema and data are optional parameters indicating the need to generate Acless schema in the database and API URLs, files and entered in the configuration respectively.

<br>

### Mechanics of work

The controller method is called from outside the controller class. How this happens is unimportant. 

If the method is public, or the comment in the method indicates the value of the *acless_no_rights_check Directive*
the configuration, the system is not involved and the execution occurs as usual. 
If the method is private (access modifiers private and protected) and if you specify a special phpdoc tag treatment method system Acless (value Directive acless_label configuration), then the database query that checks the possibility of execution of the method with the come settings for a specific user (user ID is specified when you create objects Acless).

For example:

```
class User
{
    /**
     * Get user object
     *
     * @aclessMethod
     *
     * @param int $id - user identifier
     *
     * @return UserAbstract
     */
    protected static function get(int $id): UserAbstract
    {
        // ...
    }
}
```

This example will check the current user's access to the static get method of the User class for any values of the $id argument.

However, access can be defined to execute a method with specific arguments:

```
     /**
     * Get user object
     *
     * @aclessMethod
     *
     * @aclessFilter id
     *
     * @param int $id - user identifier
     *
     * @return UserAbstract
     */
    protected static function actionGet(int $id): UserAbstract
    {
        // ...
    }
```

In this example, access will be allowed only if the value of the $id filter argument is included in the list of allowed values stored in the database (column *values*of the*access_right*table). A list in the database will look like:

`
ARRAY ['<filter value 1>, <filter value 2',...]
`

You can also filter by several arguments:

```
     /**
     * Set user role
     *
     * @aclessMethod
     *
     * @aclessFilter id, role
     *
     * @param int $id - user identifier
     * @param string $role - user role
     *
     * @return void
     */
    protected static function actionSetRole(int $id, string $role): void
    {
        // ...
    }
```

In this case, the list of allowed beginnings will have the format:

```
ARRAY ['<value for first filter><separator><value for second filter>...'....]
```

Therefore, to allow the method `'User::setRole(21,' Moderator`) `to be executed, it is necessary that the value`21:Moderator ' be in the list of allowed values, for the default switch <b>:</b>

The module supports type checking of input parameters. Php 7 supports type hinting for type checking, however, Acless acts more intelligently:

1. In PHP, method arguments and return types can have only one type:
 
`
 protected static function setRole(int $id, string $role): void
`
 
If we want a multi-type typesetting like in C# or TypeScript:
 
```
 setMultiData (object: HTMLElement | NodeList, data: Object | Object[] | string = this.data): HTMLElement | NodeList
 
```
 
then native PHP will not allow you to do this.
 
Subsystem type checking Acless can focus on *PHPDOC*and test values for multiple types if they are specified in*PHPDOC*:

```
     /**
     * Set user role
     *
     * @aclessMethod
     *
     * @aclessFilter id, role
     *
     * @param int|string $id - user identifier
     * @param string/IRole $role - user role
     *
     * @return UserAbstract|void
     */
    protected static function actionSetRole(int $id, string $role)
    {
        // ...
    }
```

2. By default, the value of an argument can be considered "correct" even if its type does not match the expected type, but the value cast to the expected type (or one of the expected) does not differ from the original in a fuzzy comparison (==). This behavior can be disabled by specifying a tag from the *deny_fuzzy* configuration Directive for the method.

This functionality is available For both controller methods such for model methods.
<br>

The module supports the generation of URLs to API methods from controller files.

It is only necessary to specify the required configuration directives in the configuration file Acless.

```
controllers:
  - path: /var/www/project/controllers
    method_prefix: action
    namespace: app\controllers
```

After generating the automatically populated table *acless.url*
<br>

In the configuration file, you can specify the roles of system users

```
roles:
  - Administrator
  - Moderator
  - Listener
  - Guest
```
Why these roles can be tied to real users registered in the system and set the default access rights for each role.

In spite of this, further access rights for any user can be changed regardless of the initial set of rights - access rights by default exist only to set it was possible to automatically give a set of rights to the user.

The module supports access rights management for private files. The mechanism is the same as for the API. In fact, the system still works it with the URLs for the methods of controllers or with the corners of private files. To generate links to the files you just need to set the config directory in which these files are stored:

```
files:
    - /var/www/project/view/private/materials
```

Additional URLs for verification can be set simply by writing them to the configuration file in the urls Directive:

```
urls:
  - /var/www/project/file.jpg
  - /var/www/project/get-a-lot-of-money
```

<br>

To work correctly with the controller methods, the controller classes to be processed must inherit from the AclessControllerParent class. To check arguments of methods models, you should inherit a model class from a class AclessModelParent.
<br>

The main data store of the system is PostgreSQL. However, the data required for the authorization check is cached in the Redis store. To increase performance. 

When you change data in the primary storage (PostgreSQL), the data in the cache (Redis) is automatically updated by the trigger. The user of the PostgreSQL process must have access to the Redis repository for the trigger to execute correctly.

<br>

#### Additional functionality:

1. Supports the addition of callbacks running before and after the successful execution of the controller method. At the same time, these flasks can change the input data and the result accordingly.

2. During initialization, the module retrieves the name of all database tables from the database. In the future, access to these tables will be handled by the rights checking subsystem. You can edit this information in the database as you like.

3. In addition to the database, you can specify the controller method type, which is also the type of API methods, to know which method will be changing, deleting, creating or reading, which can be useful for filtering the controller methods in the user interface.

<br>

[Documentation](docs_en)

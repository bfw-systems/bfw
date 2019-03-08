# Application

Like I said in [How it's work](../how-it-works/readme.md), this class is the core of the system.
It's him which initialise all other systems and send events for routers and controllers.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_CALL_UNKNOWN_METHOD`__

Exception code if `__call` is called with an unmanaged method.

__`ERR_CALL_UNKNOWN_PROPERTY`__

Exception code if `__call` is called with an unmanaged property.

__`ERR_APP_SYSTEM_CLASS_NOT_EXIST`__

Exception code if an appSystem class not exist.

__`ERR_APP_SYSTEM_NOT_IMPLEMENT_INTERFACE`__

Exception code if an AppSystem not implement `\BFW\Core\AppSystems\SystemInterface`.

## Properties

__`protected static \BFW\Application|null $instance = null;`__

The Application instance (Singleton).

__`protected \BFW\Core\AppSystems\SystemInterface[] $appSystemList = [];`__

A list of all AppSystems instance.

__`protected array $declaredOptions = [];`__

All options passed to the `initSystems` method.

__`protected \BFW\RunTasks|null $runTasks;`__

A RunTasks which contains the list of all AppSystem to execute during the run (some AppSystem only need to be initialised).

## Methods

`self protected __construct()`<br>
`\BFW\Application public static getInstance()`

This class use the [design pattern Singleton](https://en.wikipedia.org/wiki/Singleton_pattern),
so we have a protected constructor and a public method `getInstance` to call the constructor if `$instance` is null, and return the instance.

The constructor call 3 functions :
* `ob_start` to start the output buffer
* `header` to declare a default HTTP return to `text/html` with a charset `utf-8`
* `ini_set` to define the `default_charset` to `UTF-8`

### Getters

For more info about returned data, please refer to the explanation on the properties.

__`\BFW\Core\AppSystems\SystemInterface[] public getAppSystemList()`__

__`array public getDeclaredOptions()`__

__`\BFW\RunTasks|null public getRunTasks()`__

### AppSystems

__`string[] protected obtainAppSystemList()`__

Return the list of all AppSystems class (with namespace) to instantiate (by `initSystems`).
Keys in the returned array will be keys used in the property `$appSystemList` for the corresponding instance.

__`mixed public __call(string $name, array $arguments)`__

The magic method [\_\_call](http://php.net/manual/en/language.oop5.overloading.php#object.call)
is a method that will be called when an unknown method on the object is called.

In the case of Application, we use this method to have a easy access to AppSystems.<br>
The method `__call` find the asked AppSystem with the name of the called method, access to the AppSystem instance,
and return the value returned by the method `__invoke` of the asked AppSystem.

If the called method not start by `get`, an exception will be thrown;
the exception code will be the constant `\BFW\Application::ERR_CALL_UNKNOWN_METHOD`.

If the asked AppSystem not exist in the list (on property `$appSystemList`), an exception will be thrown;
the exception code will be the constant `\BFW\Application::ERR_CALL_UNKNOWN_PROPERTY`.

By default, the dynamic methods list is :
* `\Composer\Autoload\ClassLoader getComposerLoader()`  
* `\BFW\Config getConfig()`  
* `null getConstants()`  
* `object getCtrlRouterLink()`  
* `\BFW\Core\Errors getErrors()`  
* `\BFW\Memcached getMemcached()`  
* `\BFW\ModuleList getModuleList()`  
* `\BFW\Monolog getMonolog()`  
* `\BFW\Core\Options getOptions()`  
* `\BFW\Request getRequest()`  
* `null getSession()`  
* `\BFW\SubjectList getSubjectList()`

### Initialise the system

__`self public initSystems(array $options)`__

Save `$options` on the property `$declaredOptions`, and create a RunTasks instance on the property `$runTasks`.

After that, read the AppSystem list returned by method `obtainAppSystemList`.
For each AppSystem, call the method `initAppSystem` and if the AppSystem is SubjectList,
we add the RunTasks to the list of subjects with the name `ApplicationTasks`.

And a monolog message is sent when all declared AppSystem is initialised.

__`void protected initCoreSystem(string $name, string $className)`__

Doing some check on the `$className`, and if all is good, instantiate the class `$className`
and keep it on the list in the property `$appSystemList` for the key `$name`.

If the AppSystem also need to be run (value returned by the AppSystem method `toRun`),
we add the AppSystem method `run` on the RunTasks (property `$runTasks`).

If the class not exist, an exception will be thrown;
the exception code will be the constant `\BFW\Application::ERR_APP_SYSTEM_CLASS_NOT_EXIST`.

If the class not implement the interface `\BFW\Core\AppSystems\SystemInterface`, an exception will be thrown;
the exception code will be the constant `\BFW\Application::ERR_APP_SYSTEM_NOT_IMPLEMENT_INTERFACE`.

### Run the system

__`void public run()`__

Call the method `run` of the `RunTasks` kept on the property `$runTasks` to execute all AppSystem who need it.

After that, sent the event `bfw_run_done`.

# Core\AppSystems

I started to talk about AppSystem into [how-it-works page](../how-it-works/readme.md).
AppSystem is a class which initialise and start a framework subsystem.
These AppSystems is instantiated by the `\BFW\Application` class.

All AppSystem should implement the interface `\BFW\Core\AppSystems\SystemInterface`.

## SystemInterface

__`bool public toRun()`__

Some subsystem only needs to be initialised.
This method gives you the information if the subsystem also needs to be run.

__`void public run()`__

To execute/run the subsystem.

__`bool public isRun()`__

This method gives you the run status.

## AbstractSystem

Each AppSystem integrated to the framework extend this class which implement the interface `SystemInterface`.

### Properties

__`protected boolean $runStatus = false;`__

To know if the run method has been called

### Methods

__`mixed public abstract __invoke()`__

This method is a [PHP magic method](http://php.net/manual/en/language.oop5.magic.php#object.invoke)
which is called when an object is called like a function.

This method is used by `Application` class for direct access to AppSystem.
For example, when you doing `\BFW\Application::getInstance()->getModuleList()`,
this will call the method `__invoke` of the `ModuleList` AppSystem.

## ComposerLoader

### Property

__`protected \Composer\Autoload\ClassLoader $loader;`__

### Methods

__`self public __construct()`__

Obtain the `\Composer\Autoload\ClassLoader` instances from Composer and keep it into the property `$loader`.
After that, call the method `addComposerNamespaces()` to add some namespaces which can be used by the application.

__`\Composer\Autoload\ClassLoader public __invoke()`__<br>
__`\Composer\Autoload\ClassLoader public getLoader()`__

Return the value of the property `$loader`.

__`void protected addComposerNamespaces()`__

Add namespaces `\Controllers`, `\Modeles` and `\Modules` to registered PSR-4 namespaces in Composer.
  
__`string protected obtainVendorDir()`__

Obtain the path of the vendor directory from `\BFW\Application` options.

## Config

More details about `\BFW\Config` on his [dedicated page](../others-class/Config.md).

### Property

__`protected \BFW\Config $config;`__

### Methods

__`self public __construct()`__

Instantiate the object `\BFW\Config` for the config directory `/app/config/bfw` and keep it into the property `$config`.
After that, call the method `loadFiles` from Config object to load all config files in the directory.

__`\BFW\Config public __invoke()`__<br>
__`\BFW\Config public getConfig()`__

Return the value of the property `$config`.

## Constants

### Methods

__`self public __construct()`__

Add some new constants. Refer to the page [added constants](../added-constants.md) for the list.

__`null public __invoke()`__

This AppSystem does not instantiate anything, so nothing to return.

__`string protected obtainRootDir()`__

Obtain the path of the application root directory from `\BFW\Application` options.

## CtrlRouterLink

### Property

__`protected object $ctrlRouterInfos;`__

An anonymous class with info from the router to the controller system.

This class contain public properties :
* `public bool $isFound = false;`
* `public string $forWho = null;`
* `public mixed $target = null;`
* `public mixed $datas = null;`

Please refer to the page [Create a module](../how-it-works/create-module.md#special-case-of-controller-and-router-modules) for more info.

### Methods

__`self public __construct()`__

Instantiate an anonymous class and keep it into the property `$ctrlRouterInfos`.

Also instantiate a `\BFW\RunTasks` named `ctrlRouterLink` used to run tasks for search and execute current route.
The task list is returned by method `obtainCtrlRouterLinkTasks`.

This RunTasks is added into Subject list with the name `ctrlRouterLink`.
And after that, the event `bfw_ctrlRouterLink_subject_added` is notified by `ApplicationTasks` subject.

__`object public __invoke()`__<br>
__`object public getCtrlRouterInfos()`__

Return the value of the property `$ctrlRouterInfos`.
  
__`array protected obtainCtrlRouterLinkTasks()`__

Return the array which contain all tasks to execute by the RunTasks instantiate in the constructor.

__`void public run()`__

Call the method `runCtrlRouterLink` and update the run status.

__`void protected runCtrlRouterLink()`__

Call the method `run` of the RunTasks to execute all tasks.

## Errors

More details about `\BFW\Core\Errors` on his [dedicated page](./Errors.md).

### Property

__`protected \BFW\Core\Errors $errors;`__

### Methods

__`self public __construct()`__

Instantiate the object `\BFW\Core\Errors` and keep it into the property `$errors`.
This object use functions [set_error_handler](http://php.net/manual/en/function.set-error-handler.php)
and [set_exception_handler](http://php.net/manual/en/function.set-exception-handler.php)
to define personal error page (if enabled by config).

__`\BFW\Core\Errors public __invoke()`__<br>
__`\BFW\Core\Errors public getErrors()`__

Return the value of the property `$errors`.

## Memcached

More details about `\BFW\Memcached` on his [dedicated page](../others-class/Memcached.md).

### Property

__`protected \BFW\Memcached|null $memcached;`__

### Methods

__`self public __construct()`__

Call the method `loadMemcached`.

__`void protected loadMemcached()`__

If memcached is enabled into config file, instantiate the object `\BFW\Memcached` and keep it into the property `$memcached`.
After that, always if enabled, the `\BFW\Memcached` class will connect to all memcache(d) servers declared.

__`\BFW\Memcached|null public __invoke()`__<br>
__`\BFW\Memcached|null public getMemcached()`__

Return the value of the property `$memcached`.
The value will be `null` if memcached is disabled into config file.

## ModuleList

More details about `\BFW\Core\ModuleList` on his [dedicated page](./ModuleList.md).

### Property

__`protected \BFW\Core\ModuleList $moduleList;`__

### Methods

__`self public __construct()`__

Instantiate the object `\BFW\Memcached` and keep it into the property `$memcached`.

__`\BFW\Core\ModuleList public __invoke()`__<br>
__`\BFW\Core\ModuleList public getModuleList()`__

Return the value of the property `$memcached`.

__`void public run()`__

Call methods (in this order) `loadAllModules`, `runAllCoreModules`, `runAllAppModules` and update the run status.

__`void protected loadAllModules()`__

Search all modules in the project and instantiate an object `\BFW\Module` for each module.
After that, call the method to generate the module dependency tree.

__`void protected runAllCoreModules()`__

Execute all modules declared into the config file `/app/config/bfw/modules.php`.

__`void protected runAllAppModules()`__

Execute all modules which has not been already executed by the method `runAllCoreModules`.

__`void protected runModule(string $moduleName)`__

Execute a module with a call to the runner file of the module `$moduleName`.
Use the method `runModule` of the `\BFW\Module` instance for that.

## Monolog

More details about `\BFW\Monolog` on his [dedicated page](../others-class/Monolog.md).

### Property

__`protected \BFW\Monolog $monolog;`__

### Methods

__`self public __construct()`__

Instantiate the object `\BFW\Monolog` and keep it into the property `$monolog`.
Also add all handlers declared into config file `/app/config/bfw/monolog.php` to the Monolog Logger.

__`\BFW\Monolog public __invoke()`__<br>
__`\BFW\Monolog public getMonolog()`__

Return the value of the property `$monolog`.

## Options

More details about `\BFW\Core\Options` on his [dedicated page](./Options.md).

### Property

__`protected \BFW\Core\Options $options;`__

### Methods

__`self public __construct()`__

Instantiate the object `\BFW\Core\Options` and keep it into the property `$options`.

Use arguments of method `\BFW\Application::initSystems` to the `\BFW\Core\Options` constructor argument.
After that, call methods `searchPaths` and `checkPaths` (of `$options` object) to search paths (if missing) and check if paths are correct.

__`\BFW\Core\Options public __invoke()`__<br>
__`\BFW\Core\Options public getOptions()`__

Return the value of the property `$options`.

__`array protected obtainDefaultOptions()`__

Return option which has in `\BFW\Application::initSystems` argument.

## Request

More details about `\BFW\Request` on his [dedicated page](../others-class/Request.md).

### Property

__`protected \BFW\Request $request;`__

### Methods

__`self public __construct()`__

Instantiate the object `\BFW\Request` and keep it into the property `$request`.

__`\BFW\Request public __invoke()`__<br>
__`\BFW\Request public getRequest()`__

Return the value of the property `$request`.

## Session

### Methods

__`self public __construct()`__

If session should be started (declared in `\BFW\Application::initSystems` argument), these actions will be done :
1. Call the function [session_set_cookie_params](http://php.net/manual/en/function.session-set-cookie-params.php)
with the `lifetime` parameter to 0; this will destroy the session cookie when the browser is closed.
2. Start the session with a call to [session_start](http://php.net/manual/en/function.session-start.php).

__`null public __invoke()`__

This AppSystem does not instantiate anything, so nothing to return.

__`bool protected obtainRunSession()`__

Obtain the value of `runSession` option.

## SubjectList

More details about `\BFW\Core\SubjectList` on his [dedicated page](./SubjectList.md).

### Property

__`protected \BFW\Core\SubjectList $subjectList;`__

### Methods

__`self public __construct()`__

Instantiate the object `\BFW\Core\SubjectList` and keep it into the property `$subjectList`.

__`\BFW\Core\SubjectList public __invoke()`__<br>
__`\BFW\Core\SubjectList public getSubjectList()`__

Return the value of the property `$subjectList`.

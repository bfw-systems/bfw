# How it works ?

Into the file `web/index.php`, we initialise the class `\BFW\Application`, who initialise many subsystems and run them.

The class `Application` use the [design pattern Singleton](https://en.wikipedia.org/wiki/Singleton_pattern),
so you can get the instance from anywhere into your application.

At the beginning, in front files (`web/index.php`), we create the instance and we run the main initialisation.
```php
//Initialise BFW application
$app = \BFW\Application::getInstance();
$app->initSystems([
    'rootDir'   => $rootDir,
    'vendorDir' => $vendorDir
]);
```
The method `initSystems` will execute the initialisation of all framework systems.<br>
This parameter is an array; Possible keys are :
* `string rootDir` : Path to the root directory of the project.
If the key is not declared, the system will try to find the path itself
(but I prefer to declare the value to avoid the search into directories and IO times).
* `string vendorDir` : Path to the composer vendor directory.
Like `rootDir`, if not declared, the system will try to find the path itself.
* `bool runSession` : If the system should run the PHP session automatically.

During the call to `initSystems`, many subsystems (called AppSystems) will be initialised.

First, we obtain the list of all AppSystems to initialise with the method `obtainAppSystemList`.
The list of existing AppSystems is into the directory `/src/Core/AppSystems`,
and all AppSystems should implement the interface `\BFW\Core\AppSystems\SystemInterface`.

Next, an instance of the class `\BFW\RunTasks` is created (it will be used later during the call to the method `run`).<br>
And after, all subsystems defined is checked and instantiated (in the order of the list into the array returned by `obtainAppSystemList`).
If a subsystem declares that it will need to be launched too,
it's added to the list of events to run in the instance of `\BFW\RunTasks` created before.

By default, the list (and what they are doing) of subsystems is :
* Options : Obtain the path to the project root directory and to the vendor directory.
* Constants : Define constants for each directory present with the default skeleton of the framework.
* ComposerLoader : Obtain the composer's Loader instance and add some namespaces.
* SubjectList : Instantiate the class who will contain the list of all subjects  ([design pattern observer](https://en.wikipedia.org/wiki/Observer_pattern))
who are being defined (the addition to the list is not automatic).
* Config : Read and load the framework config (directory `app/config/bfw/`).
* Monolog : Read the config into the file `monolog.php`, instantiate the [Monolog](https://github.com/Seldaek/monolog) logger,
and define all handlers define into the config.
* Request : Instantiate class `\BFW\Request` ([design pattern Singleton](https://en.wikipedia.org/wiki/Singleton_pattern))
and detect many things about the current HTTP request.
* Session : Run the PHP session if the parameter `initSystems` not say not to run it.
* Errors : Initialise the system to display personal error page instead of blank pages if the config into `errors.php` allow it.
* Memcached : Connect to all memcache(d) servers defined into config file `memcached.php`.
* ModuleList : Initialise the class who will list all modules, run the search for all modules and initialise each module (not run it).
* CtrlRouterLink : Define the object which will be used to communicate between controller and router modules.
And instantiate an `\BFW\RunTasks` object which will have all tasks to run when the controller system will have to be executed.

Ok so now, the framework and all subsystems is initialised. We can run it.
```php
$app->run();
```

What it's doing can be summarised to the call to the method `run` of the `\BFW\RunTasks`
object which has been defined at the beginning of the method `initSystem`.
That will run all subsystems which has declared the need to be run.

In the order, it's :
* ModuleList : Generate the dependency tree of all modules and run modules in the order defined by the tree
* CtrlRouterLink : Only if it's not from cli, execute the `\BFW\RunTasks` object (defines during this init) with the method `run`.
That will notify all events needed by router and controller systems to be executed.

And it's all. At this time, your controller has ended to be executed.

Because Monolog is integrated, all step is logged.
It's handlers define into `monolog.php` config file who receive messages and log it.
So the location of the log depends on your handler config.

This is an example of the log :

To obtain this example, I have used the example for [web script](../get-started/example-scripts.md#web-script) and I have enabled the
file handler into config (the commented handler into `monolog.php` config file).
Like you can see, some modules can use the bfw logger to log this own event.
In the case you can see here, it's to have a better visibility of when the event is run.

```
$ cat app/logs/bfw/bfw.log 
[2018-08-21 23:16:20] bfw.DEBUG: Currently during the initialization framework step. [] []
[2018-08-21 23:16:20] bfw.DEBUG: RunTask notify {"prefix":"BfwApp","action":"bfw_ctrlRouterLink_subject_added"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"bfw_ctrlRouterLink_subject_added"} []
[2018-08-21 23:16:20] bfw.DEBUG: Framework initializing done. [] []
[2018-08-21 23:16:20] bfw.DEBUG: running framework [] []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"BfwApp_start_run_tasks"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"BfwApp_run_moduleList"} []
[2018-08-21 23:16:20] bfw.DEBUG: New module declared {"name":"bfw-controller"} []
[2018-08-21 23:16:20] bfw.DEBUG: Load module {"name":"bfw-controller"} []
[2018-08-21 23:16:20] bfw.DEBUG: New module declared {"name":"bfw-fastroute"} []
[2018-08-21 23:16:20] bfw.DEBUG: Load module {"name":"bfw-fastroute"} []
[2018-08-21 23:16:20] bfw.DEBUG: RunTask notify {"prefix":"BfwApp","action":"BfwApp_run_module_bfw-controller"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"BfwApp_run_module_bfw-controller"} []
[2018-08-21 23:16:20] bfw.DEBUG: RunTask notify {"prefix":"BfwApp","action":"BfwApp_run_module_bfw-fastroute"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"BfwApp_run_module_bfw-fastroute"} []
[2018-08-21 23:16:20] bfw-fastroute.DEBUG: Add all routes. [] []
[2018-08-21 23:16:20] bfw.DEBUG: RunTask notify {"prefix":"BfwApp","action":"BfwApp_run_module_bfw-controller"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"BfwApp_run_module_bfw-controller"} []
[2018-08-21 23:16:20] bfw.DEBUG: RunTask notify {"prefix":"BfwApp","action":"BfwApp_run_module_bfw-fastroute"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"BfwApp_run_module_bfw-fastroute"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"BfwApp_done_moduleList"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"BfwApp_run_ctrlRouterLink"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"ctrlRouterLink_start_run_tasks"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"ctrlRouterLink_exec_searchRoute"} []
[2018-08-21 23:16:20] bfw-fastroute.DEBUG: Search the current route into declared routes. {"request":"/test","method":"GET","status":1} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"ctrlRouterLink_run_checkRouteFound"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"ctrlRouterLink_done_checkRouteFound"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"ctrlRouterLink_exec_execRoute"} []
[2018-08-21 23:16:20] bfw-controller.DEBUG: Execute current route. {"target":{"class":"\\Controller\\Test","method":"index"}} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"ctrlRouterLink_end_run_tasks"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"BfwApp_done_ctrlRouterLink"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"BfwApp_end_run_tasks"} []
[2018-08-21 23:16:20] bfw.DEBUG: RunTask notify {"prefix":"BfwApp","action":"bfw_run_done"} []
[2018-08-21 23:16:20] bfw.DEBUG: Subject notify event {"action":"bfw_run_done"} []
```

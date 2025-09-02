# Create a module

Because modules can be used to add business code, you can have the need to create a module. Don't worry, it's easy ;)

You need two files to create a module :
* `bfwModulesInfos.json` : To know how to install it
* `moudule.json` : To know how to execute it

After that, you can add many files :
* config files
* runner file
* anything more

## The file bfwModulesInfos.json

This file is used by the framework to know how to install the module.

For example, with the file used for the module [bfw-hello-world](https://github.com/bulton-fr/bfw-hello-world).
This file contains all property that can be declared.

```json
{
    "srcPath": "src/",
    "configPath": "config/",
    "configFiles": [
        "hello-world.json"
    ],
    "installScript": ""
}
```

And the skeleton of the module :
```
├── bfwModulesInfos.json
├── composer.json
├── config
│   ├── manifest.json
│   └── hello-world.json
├── LICENSE
├── README.md
└── src
    ├── helloWorld.php
    └── module.json
```

To explain each property :
* `srcPath` (mandatory) : The path of source files used for module execution
* `configPath` : The path to config files to copy during the installation.
If not declared, the path used is the path declared for `srcPath`.
* `configFiles` : The list of all config file to copy.
If not declared, no files while be copied.
The file `manifest.json` not need to be declared, it will be copied automatically.
* `installScript` : An installation script to run after the module install.
If not declared, no script will be run.

For config file, only php and json files are read. Yaml files while be added later.

## The file module.json

This file is used to know how to execute the module.
It defines all dependency between modules and the main file to execute.

Always with the module [bfw-hello-world](https://github.com/bulton-fr/bfw-hello-world), this `module.json` file :
```json
{
    "runner": "helloWorld.php",
    "priority": 0,
    "require": []
}
```

The complete list of all available properties :
* `runner` : It's the main php file to execute when module is run.
* `require` : It's the list of all modules who should be loaded and run before your module.
* `priority` : It's the order priority compared to other modules (a low value means to be executed earlier).
However, if a required module has a higher value, your module will take this value.
* `needMe` : it's the opposite of `require`.
Sometimes, it can be useful to say that another module needs us to be loaded.

The property `priority` is an integer who defines the execution order of modules.
The lower the number, the more priority my module will have.
So, a 0 value will say the module have the full priority before others.
This can be used to define an execution order for modules that do not depend on each other.

Regarding the property `needMe`, it has been added to solve the [issue #70](https://github.com/bulton-fr/bfw/issues/70).<br>
The example to explain the problem is that :
I use the [bfw-api](https://github.com/bulton-fr/bfw-api) module.
I also use a personal authentication module. I want anybody can use the api if there are not logged.
For that, I need to execute my authentication module before bfw-api.
<br>
The first solution is to edit the property `require` of bfw-api to add my auth module into the list.
But bfw-api is an external module loaded by composer, so I can't modify the file (technically I can't, but it's dirty).
To modify it, I should fork the module, and I don't want to do that (fork a repo to edit one file for one specific project...).
<br>
The next solution is to use the property `priority`.
Declare my module to 0 and hooping... But bfw-api has also 0 for value, so it does not work either.
<br>
So in this case, I do not have a solution. It's for that the property `needMe` has been added.
With that, I can declare into my authentication module that the module bfw-api need me.
When the system load modules, it will add my authentication module into property `require` of bfw-api (dynamically, not into the file).
So my module will always be executed before :)

Only for information, the dependency tree system used is [dependency-tree](https://github.com/bulton-fr/dependency-tree).

## The file manifest.json

If you have config files, the file `manifest.json` can be useful for you.
It has been added to answer the [issue #78](https://github.com/bulton-fr/bfw/issues/78).
The problem is : If a user install my module, and later I will change config file structure (adding or removing key(s)).
How users will know that when it does a composer update ? How its config file will be updated ?

The solution is found, and it uses a file to know current state of config file into application (the file `manifest.json`).
The idea is when update is doing, to compare info into manifest.json file between application and repository and run update script if needed.

However, the system has not been implemented yet.
So the user will do to update config file manually for the moment.
But I prefer to add this file now to be sure the user has the file to compare when the system will add.

For the moment, there is not much info into the file.
Maybe more info will be added later when the system will be implemented.

For example, the `manifest.json` file used for bfw :
```json
{
    "errors.php": {
        "version": "3.0.0",
        "scriptsPlayed": []
    },
    "global.php": {
        "version": "3.0.0",
        "scriptsPlayed": []
    },
    "memcached.php": {
        "version": "3.0.0",
        "scriptsPlayed": []
    },
    "modules.php": {
        "version": "3.0.0",
        "scriptsPlayed": []
    },
    "monolog.php": {
        "version": "3.0.0",
        "scriptsPlayed": []
    }
}
```

It's an object where all config file is listed.
For each file, the current version number and the list of update script played.

## How my module will work ?

When `\BFW\Application` is initialised, all modules are loaded into an instance of `\BFW\Module`.
Each module has this own instance.

When a module is loaded, each config file of the module will be read by an instance of `\BFW\Config`.

And later, when Application is running, all modules are executed.
For that, the script declared into the property `runner` of the file `module.json` will be executed.
This file is executed into a [closure](http://php.net/manual/fr/functions.anonymous.php) into the method `runModule` of `\BFW\Module`.
A closure is used to avoid some scope conflicts into runner script.
So it has this own scope; but because the closure is into a method, you have access to `$this` of the `\BFW\Module` instance for your module.
With that, you can access to all properties and methods of the instance.
And also, it's an easy way to define new properties (but it will be public) to access to some data outside of runner scripts.

### Using case with bfw-fenom

The file `module.json` is light :
```json
{
    "runner": "runner.php"
}
```

And the runner.php file :
```php
$config = $this->getConfig();

$this->fenom = Fenom::factory(
    $config->getValue('pathTemplate'),
    $config->getValue('pathCompiled'),
    $config->getValue('fenomOptions')
);
```

With that, I give access to the return of `Fenom::Factory()`.
So it's possible to access to it from the controller to add vars who will be sent to the template (for example).

To access to the property `fenom` : `\BFW\Application::getInstance()->getModuleList()->getModuleByName('bfw-fenom')->fenom`.

However, you should keep in mind this property is public, so everybody can change this value.

## Access to the config

Like I said before, all config file is read when module is loaded.
There is an instance of `\BFW\Config` dedicated for your module that contains all config keys.

Into the `\BFW\Module` instance, there is a property `config` who contains the instance of `\BFW\Config` for your module.
So, like the example before with bfw-fenom, you have access to the config with the method `getConfig()`.

Into your runner file, you can do : `$this->getConfig()`.<br>
From anywhere else, you can do : `\BFW\Application::getInstance()->getModuleList()->getModuleByName('myModule')->getConfig()`.

So like you can see, everybody (other modules, controllers, etc) can access your module config.

For more info about how to use the `\BFW\Config` class, please refer to [dedicated page](../others-classes/Config.md).

## Some getters to module info

The class `\BFW\Module` contain many methods, but only some methods can interest you.
I will talk about their methods; for others, please refer to [dedicated page about Module class](../others-classes/Module.md).

Methods are public, so you can access it from everywhere. But properties is protected, so you can access it only from your runner file.

### getName() and name

Contain/return the module name.

### getLoadInfos() and loadInfos

Contain/return the content of the file `module.json` after a [json_decode](http://php.net/manual/en/function.json-decode.php).

## Special case of controller and router modules

These modules are a little special because they need to know how to work together and stay independent.
And a bonus, there can be several routers or controllers into the same project.
For example, the module [bfw-api](https://github.com/bulton-fr/bfw-api/) you have a controller and router system,
but dedicated to api pages, so that cannot be used like controller/router system for website pages.
So in this case, we are two independent controllers and routers systems into the same application.

That is why there is the subsystem `CtrlRouterLink`.
It gives an object for controllers and routers to communicate together.<br>
To access it : `\BFW\Application::getInstance()->getCtrlRouterLink();`

This object contains 4 public properties :
* `bool $isFound` : To know if a router has found the current route
* `string $forWho` : To know who are the controller who will use the route
* `mixed $target` : The route to use
* `mixed $datas` : More info about the route

This subsystem also creates an instance of `\BFW\RunTasks` who contains events used to search and execute the current route.
4 events will be notified : 
1. `ctrlRouterLink_exec_searchRoute`
2. `ctrlRouterLink_run_checkRouteFound` and `ctrlRouterLink_done_checkRouteFound`
3. `ctrlRouterLink_exec_execRoute`

The first is for router modules. It's on this event that the route will be searched.

The second is a callback function who sends a 404 HTTP status if the property `$idFound` equal to `false`.
I prefer that is the subsystem who check if no route has been found because if it's a router, there can be some problems.
For example, if there are two routers, the first found the route, but the second not found the route and send a 404.
So what append ? The page will be displayed with a 404 status ? Not great...

And the third is for controller modules. It's on this event that the route will be executed.

This 3 events are notified by the `ctrlRouterLink` RunTasks.
So your observer class should be attached to this subject.
For that :
```php
\BFW\Application::getInstance()
    ->getSubjectList()
        ->getSubjectByName('ctrlRouterLink')
            ->attach($myObserver)
; 
```

Regarding the property `$forWho`, each controller module should implement it and check if the route is for him.

Here is the way I use for my module :
```php
namespace BfwApi;

class BfwApi implements \SplObserver
{
    //Some things...
    
    /**
     * @var string $execRouteSystemName The name of the current system. Used on
     * event "execRoute". Allow to extends this class in another module :)
     */
    protected $execRouteSystemName = 'bfw-api';

    //Some things...

    /**
     * Observer update method
     * 
     * @param \SplSubject $subject
     * 
     * @return void
     */
    public function update(\SplSubject $subject)
    {
        if ($subject->getAction() === 'ctrlRouterLink_exec_searchRoute') {
            $this->obtainCtrlRouterInfos($subject);
            
            if ($this->ctrlRouterInfos->isFound === false) {
                $this->searchRoute();
            }
        } elseif ($subject->getAction() === 'ctrlRouterLink_exec_execRoute') {
            if (
                $this->ctrlRouterInfos->isFound === true &&
                $this->ctrlRouterInfos->forWho === $this->execRouteSystemName
            ) {
                $this->execRoute();
            }
        }
    }

    //Some things...
}
```

I use a property `$execRouteSystemName` who contains the name of the system who are being used into `$forWho`.
Next when I receive the event for route execution, I check if `$forWho` is equal to `$execRouteSystemName`.
If it's not equal, so the route is not for him.

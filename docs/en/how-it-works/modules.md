# Modules

## What is a module ?

It's a set of code that can be used to instantiate library or integrate business code.

All the code executed by a module has its own scope (the `\BFW\Module` class instance associates to this module).

## Add a module to the application

Like seeing in the page [Add a module](../get-started/add-a-module.md), there are some commands for that.

All command presented here have a help if you use `-h` or `--help` options. Please refer to it.  
You can do an action for all modules or only for once if you want.  
If you have an issue with the application or vendor path which is not detected, please use options `-b` or `-V` (refer to help for detail).

### Add it

If it's an external module, you can use the command `./vendor/bin/bfwAddMod` (see page [Add a module](../get-started/add-a-module.md) for more details).  
Config directory and files will be copied (if not already exists) at this time.

If it's an internal module, just add it into the directory `/app/modules/available`.

### Enable it

A module into the `available` directory is not loaded by the framework, you need to have it into the directory `enabled` for that.

Please use the command `./vendor/bin/bfwEnMod` for that.

### Disable it

This will delete the module from the `enabled` directory. So it will not be loaded by the framework.

Please use the command `./vendor/bin/bfwDisMod` for that.

### Delete it

This will delete the module from the `available` directory. This will also delete config directory and files dedicated to this module.

Please use the command `./vendor/bin/bfwDelMod` for that.

## Access to a module

To explain with an example, I will use an easy module : [bfw-hello-world](https://github.com/bulton-fr/bfw-hello-world).
I use this module for examples or for test scripts.

Add the module :
```
$ composer require bulton-fr/bfw-hello-world
Using version ^1.0 for bulton-fr/bfw-hello-world
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 1 install, 0 updates, 0 removals
  - Installing bulton-fr/bfw-hello-world (1.0.1) Downloading: 100%         
Writing lock file
Generating autoload files
$ ./vendor/bin/bfwAddMod -- bfw-hello-world
> Add module bfw-hello-world ... Done
> Execute install script for bfw-hello-world ... No script, pass.
$ ./vendor/bin/bfwEnMod -- bfw-hello-world
> Enable module bfw-hello-world ... Done
```

Now, if I want to access to the module bfw-hello-world, I need to get the instance of `\BFW\Module` associate to this module.
```php
$app        = \BFW\Application::getInstance();
$helloWorld = $app->getModuleList()->getModuleByName('bfw-hello-world');
```

Now I have access to all public properties and methods of this module.
For more info about methods, please refer to the [dedicated page of this class](../others-classes/Module.md).

Public properties its properties define by module runner script.

For example, the module [bfw-fenom](https://github.com/bulton-fr/bfw-fenom/tree/2.0)
(who are an interface for template system [Fenom](https://github.com/fenom-template/fenom))
define the property `fenom` to access to the fenom instance.

```php
$this->fenom = Fenom::factory(
    $config->getValue('pathTemplate'),
    $config->getValue('pathCompiled'),
    $config->getValue('fenomOptions')
);
```

And to access it from anywhere :

```php
$fenom = \BFW\Application::getInstance()
    ->getModuleList() //Get the list of all modules
        ->getModule('bfw-fenom') //Access to module bfw-fenom
            ->fenom; //Access to the return of Fenom::Factory(...);
```

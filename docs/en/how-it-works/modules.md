# Modules

## What is a module ?

It's a set of code that can be used to instantiate library or integrate business code.

All the code executed by a module has its own scope (the `\BFW\Module` class instance associates to this module).

## Installation

For that, please refer to the page [Add a module](../get-started/add-a-module.md).

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
$ ./vendor/bin/bfwInstallModules 
Run BFW Modules Install
bfw-hello-world : Run install.
 > Create symbolic link ... Done
 > Copy config files : 
 >> Create config directory for this module ... Done
 >> Copy manifest.json ... Done
 >> Copy hello-world.json ... Done
 > Check install specific script :
 >> No specific script declared. Pass
Read all modules to run install script...
 > Read for module bfw-hello-world
 >> No script to run.
All modules have been read.
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

# Add a module

A module is not an essential thing to use the framework.
However, for your applications, you will probably have to add a module.

With module, there are two cases : Use an external module, or create your own module.

## An external module

For example, I will use a module dedicated to that (examples) : the module "[hello-world](https://github.com/bulton-fr/bfw-hello-world)".

Add him with composer :

```
$ composer require bulton-fr/bfw-hello-world 1.0.x-dev
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 1 install, 0 updates, 0 removals
  - Installing bulton-fr/bfw-hello-world (1.0.x-dev 0f86364) Cloning 0f86364a0e from cache
Writing lock file
Generating autoload files
```

Install it :
The installation creates a symbolic link between the source into vendor (`/vendor/bulton-fr/bfw-hello-world`) to the module installed path (`/app/modules/available/bfw-hello-world`).

```
$ ./vendor/bin/bfwAddMod -- bfw-hello-world
> Add module bfw-hello-world ... Done
> Execute install script for bfw-hello-world ... No script, pass.
```

Enable it
The enabling create a symbolic link between the directory into available (`/app/modules/available/bfw-hello-world`) to the module enabled path (`/app/modules/enabled/bfw-hello-world`).

```
$ ./vendor/bin/bfwEnMod -- bfw-hello-world
> Enable module bfw-hello-world ... Done
```

It's done, the module is installed and enabled, so it will by automatically loaded by the framework.  
If the module is only installed but not enabled, it will not by loaded by the framework.

If some things should be done after install (like filling config files), this will be written into module doc.

## An internal module

I will not explain here, there is a dedicated page [create a module](../how-it-works/create-module.md) for that : 

To summarise the idea, create a directory into `app/modules/available` with your module name.
Next create the file `module.json` into it. This file will say to the framework how to load your module.  
And enable the module with the command `./vendor/bin/bfwEnMod` (like an external module).
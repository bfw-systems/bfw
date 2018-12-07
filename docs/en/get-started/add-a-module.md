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

Install it

```
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

It's done, the module is installed and will by automatically loaded by the framework.

If some things should be done after install (like filling config files), this will be written into module doc.

## An internal module

I will not explain here, there is a dedicated page [create a module](../how-it-works/create-module.md) for that : 

To summarise the idea, create a directory into `app/modules` with your module name.
Next create the file `module.json` into it. This file will say to the framework how to load your module.

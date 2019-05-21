# Install\ModuleManager

This class manage all action for a (or all) module(s) into the project. All parameters about the action are sent to this class from binaries and sent to `\BFW\Install\ModuleManager\Actions` class. To be simple, it's a link between binaries and `Actions` class.

## Properties

__`protected string $action = '';`__

The action to do

__`protected boolean $reinstall = false;`__

To force a complete reinstall of the module

__`protected boolean $allModules = '';`__

If the action is for all modules

__`protected string $specificModule = '';`__

If the action is only for this module

## Methods

### Getters and Setters

For more info about returned data, please refer to the explanation on the properties.

__`string public getAction()`__  
__`self public setAction(string $action)`__

__`bool public getReinstall()`__  
__`self public setReinstall(bool $reinstall)`__

__`bool public getAllModules()`__  
__`self public setAllModules(bool $allModules)`__

__`string public getSpecificModule()`__  
__`self public setSpecificModule(string $specificModule)`__

### Execute the asked action

__`void public doAction()`__

Obtain the `\BFW\Install\ModuleManager\Actions` by the method `obtainActionClass()` and call the method `doAction` on the Actions class to execute the action.

This method is called from AppSystems.  
In the defined scenario, the Application is initialised from binaries, so this class is instanced at this time. After that the binary send all data to this class by setters. And after that, call the Application::run to execute all AppSystem, so call this method.

### Obtain the action class

__`\BFW\Install\ModuleManager\Actions public obtainActionClass()`__

Instanciate the Actions class and return it.  
This is to allow someone who wants to override `Actions` class to do that.

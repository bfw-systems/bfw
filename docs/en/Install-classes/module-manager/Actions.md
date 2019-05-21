# Install\ModuleManager\Actions

This class manage all action available for a module, and execute it for each module concerned by the action.

## Constant

### Exception code

These constants are sent when an exception is thrown and used like exception code.

__`EXCEP_MOD_NOT_FOUND`__

Exception code if the asked module has not been found.

## Properties

__`protected \BFW\Install\ModuleManager $manager;`__

Path to the application root project

__`protected string[string] $modulePathList = [];`__

List of all path for all modules found.  
The key is the module name, the value the absolute path.

__`protected \BFW\Install\ModuleManager\Module[string] $moduleList = [];`__

List of all module found.  
The key is the module name, the value the instance of `\BFW\Install\ModuleManager\Module` for the module.

## Methods

`self public __construct(\BFW\Install\ModuleManager $manager)`

Take for argument the instance of the ModuleManager, and keep it on property `$manager`.

### Getters

For more info about returned data, please refer to the explanation on the properties.

__`\BFW\Install\ModuleManager public getManager()`__

__`string[string] public getModulePathList()`__

__`\BFW\Install\ModuleManager\Module[string] getModuleList()`__

### Execute an action

__`void public doAction()`__

Obtain the current asked action, and call the method dedicated to this action.  
The list are:

* add : `doAdd`
* enable : `doEnable`
* disable : `doDisable`
* delete : `doDelete`

__`void protected doAdd()`__

Execute all actions for an add of module(s).  
So it will search all modules in vendor, add module(s) asked (with the action `doAdd` on the `\BFW\Install\ModuleManager\Module` class), and run the install script.

If the reinstall status is set to `true`, there is a call to `doDelete` at the begining.

__`void protected doEnable()`__

Search all modules in `/app/modules/available` and enable asked modules with the action `doEnable` on the `\BFW\Install\ModuleManager\Module` class.

__`void protected doDisable()`__

Search all modules in `/app/modules/available` (it's not a copy/paste error) and disable asked modules with the action `doDisable` on the `\BFW\Install\ModuleManager\Module` class.

__`void protected doDelete()`__

Search all modules in `/app/modules/available` (it's not a copy/paste error) and delete asked modules with the action `doDelete` on the `\BFW\Install\ModuleManager\Module` class.

### Search all modules

__`void protected function obtainModulePathList(string $dirPath)`__

Obtain the list of all modules by a call to `searchAllModulesInDir`. And for each path, extract the name of the module and add the module path to the list on the property `$modulePathList`.  
At the end, a sort (with `ksort`) is done on the property `$modulePathList`.

__`void protected function searchAllModulesInDir(string $dirPath)`__

Instantiate the class `BFW\Install\ReadDirLoadModule` to find all directories which have a file `bfwModulesInfos.json` and return the list of paths found.

### Execute the asked action

__`void protected function executeForModules(string $actionMethodName, string $actionName)`__

Call the method `actionOnModule` for each module concerned by the action to do.

At the end, a sort (with `ksort`) is done on the property `$moduleList`.

__`void protected function actionOnModule(string $moduleName, string $modulePath, string $actionMethodName, string $actionName)`__

Call the method `obtainModule` to obtain the dedicated instance of `\BFW\Install\ModuleManager\Module` for the module. Define the path of the source of the module (in `/vendor` or directly in `/app/modules/available`), and call the method `$actionMethodName` into the `Module` class to do the action for the module.

After that, the module instance will be added to the property `$moduleList`.

If `$modulePath` is empty, the path will be obtained from the property `$modulePathList`. If the module is not found into it, an `\Exception` will be thrown with the exception code `\BFW\Install\ModuleManager\Actions::EXCEP_MOD_NOT_FOUND`.

__`\BFW\Install\ModuleManager\Module protected function obtainModule(string $moduleName)`__

Return the instance dedicated to the module.  
It's a method to do that to allow users to easy override the class to use for Module.

### Execute the install script

__`void protected function runInstallScript(\BFW\Install\ModuleManager\Module $module)`__

Check if the module has an installation script, and call the method to execute it.

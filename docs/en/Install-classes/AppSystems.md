# Install\Core\AppSystems

These classes are AppSystems dedicated to the installation system.
Like Core\AppSystems, these classes extend `\BFW\Core\AppSystems\AbstractSystem` which implements `\BFW\Core\AppSystems\SystemInterface`.

For more info about AppSystems, please refer to [Core\AppSystem page](../core-classes/AppSystems.md)

## ModuleList

Override of [\BFW\Core\AppSystems\ModuleList](../core-classes/AppSystems.md#ModuleList).

### Methods

__`void public run()`__

Only call methods `loadAllModules` before update the run status.

All run methods are not called. **So no module is executed when the system install other modules.**

## ModuleInstall


### Property

__`protected \BFW\Install\ModuleInstall[] $listToInstall;`__

List of all modules to install.

### Methods

__`self public __invoke()`__<br>
__`\BFW\Install\ModuleInstall[] public getListToInstall()`__

Return the value of the property `$listToInstall`.

__`self public addToList(\BFW\Install\ModuleInstall $module)`__

Add a new module in the list on the property `$listToInstall`.

__`void public run()`__

Call the method `installAllModules` to run the complementary install of all modules in the list and update the run status.

`void protected installAllModules()`

Obtain the dependency tree of all modules and read it.
If the module read is also in the list on the property `$listToInstall`, so call the method `installModule` for him.

`void protected installModule(string $moduleName)`

Call the method `runInstallScript` from `\BFW\Install\ModuleInstall` for the module `$moduleName`
to execute the personalised install script for this module.

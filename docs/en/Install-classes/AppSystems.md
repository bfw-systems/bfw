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

## ModuleManager

### Property

__`protected \BFW\Install\ModuleManager $manager;`__

Instance of the module manager which run action for modules.

### Methods

__`\BFW\Install\ModuleManager public __invoke()`__  
__`\BFW\Install\ModuleManager public getManager()`__

Return the value of the property `$manager`.

__`void public run()`__

Call the method `doAction` on `$manager` to run the action asked from binaries files. If no action has been asked, nothing will be done.

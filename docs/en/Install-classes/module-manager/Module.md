# Install\ModuleManager\Module

This class do an action (add, enable, disable, delete) for a module.

## Constant

### Exception code

These constants are sent when an exception is thrown and used like exception code.

__`EXCEP_DELETE_ENABLED_MODULE`__

Exception code if the user wants to delete a module which is always enabled.

## Properties

__`protected \Monolog\Logger $logger;`__

The monolog logger instance.

__`protected \bultonFr\Utils\Files\FileManager $fileManager;`__

The FileManager instance used to do action on files.

__`protected string $vendorPath = '';`__

The path to the source of the module in the vendor directory.
The value is set by a setter only, so is possible the value is not into the vendor directory if the user not send that.

Used only with the add action.

__`protected string $availablePath = '';`__

The path of the module into /app/modules/available folder.

__`protected string $enabledPath = '';`__

The path of the module into /app/modules/enabled folder.

__`protected string $configPath = '';`__

The path of the config module into /app/config folder.

__`protected \BFW\Install\ModuleManager\ModuleInfo|null $info;`__

The class which contain info about the module.  
This property is populated by method `readModuleInfo` which is called at the beginning of each action methods.

## Methods

`self public __construct(string $name)`

Take for argument the module name, and keep it on property `$name`.

With the name, the value of properties `availablePath`, `enabledPath`, `configPath` is generated.  
The value of properties `logger` and `fileManager` is also populated at this time.

### Getters

For more info about returned data, please refer to the explanation on the properties.

__`\Monolog\Logger public getLogger()`__

__`\bultonFr\Utils\Files\FileManager public getFileManager()`__

__`string getName()`__

__`string getVendorPath()`__  
__`self setVendorPath(string $path)`__

__`string getAvailablePath()`__

__`string getEnabledPath()`__

__`string getConfigPath()`__

__`\BFW\Install\ModuleManager\ModulesInfo getInfo()`__

### Execute an action

__`void protected doAdd()`__

Call the method `readModuleInfo` to obtain info about the module.
After that, create the symbolic link from module directory in vendor into `/app/modules/available`. And finish with a copy of all config files declared.

__`void protected doEnable()`__

Call the method `readModuleInfo` to obtain info about the module.
After that, create the symbolic link from module directory in `/app/modules/available` into `/app/modules/enabled`.

__`void protected doDisable()`__

Call the method `readModuleInfo` to obtain info about the module.
After that, delete the symbolic link for the module into `/app/modules/enabled`.

__`void protected doDelete()`__

Call the method `readModuleInfo` to obtain info about the module.
After that, delete the symbolic link (or the directory) for the module into `/app/modules/available`. And finish with a delete of config directory dedicated to the module (if exist).

### Obtain module info

__`void protected function readModuleInfo(string $dirPath)`__

Obtain the decoded info about the module (json_decode), and pass info to a new instance of the class `\BFW\Install\ModuleManager\ModuleInfo` which will keep into the property `$info`.

### Execute an action on file

__`void protected function copyAllConfigFiles()`__

Create the dedicated directory for the module in `/app/config` if it's not already exist.

After that, use the method `copyConfigFile` to copy the `manifest.json` file and all config files declared into the created directory.

__`void protected function copyConfigFile(string $sourcePath, string $destPath)`__

Use the method `copyFile` from the `$fileManager` to copy the `$sourcePath` to the `$destPath`.

__`void protected function deleteConfigFiles()`__

Delete the dedicated config directory for the module into `/app/config` (if exist).

### Install script

__`bool protected function hasInstallScript()`__

Check if the module has an installation script declared.

__`void protected function runInstallScript()`__

Execute the installation script declared.

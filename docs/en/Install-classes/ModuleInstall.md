# Install\ModuleInstall

This class manage the installation of a module into the project.
It read the file `bfwModulesInfos.json` and execute the installation script.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_LOAD_NO_PROPERTY_SRCPATH`__

Exception code if the property `srcPath` is not present into the `bfwModulesInfos.json` file.

__`ERR_REINSTALL_FAIL_UNLINK`__

Exception code if the reinstall fail because the module symlink cannot be removed.

__`ERR_REINSTALL_FAIL_REMOVE_CONFIG_DIR`__

Exception code if the reinstall fail because the config directory cannot be removed.

__`ERR_COPY_CONFIG_SRC_FILE_NOT_EXIST`__

Exception code if the source config file not exist.

__`ERR_COPY_CONFIG_FAIL`__

Exception code if the copy of the config file has failed.

__`ERR_LOAD_EMPTY_PROPERTY_SRCPATH`__

Exception code if the property `srcPath` is empty during the load of the module

__`ERR_LOAD_PATH_NOT_EXIST`__

Exception code if the path defined into `srcPath` or `configPath` not exist

__`ERR_INSTALL_FAIL_SYMLINK`__

Exception code if the symlink fail

__`ERR_FAIL_CREATE_CONFIG_DIR`__

Exception code if the config directory cannot be created.

## Properties

__`protected string $projectPath = '';`__

Path to the application root project

__`protected boolean $forceReinstall = false;`__

To force a complete reinstall of the module

__`protected string $name = '';`__

The module name

__`protected string $sourcePath = '';`__

Path to the module which be installed

__`protected string $sourceSrcPath = '';`__

Path to the directory which contains files to install into the project module directory

__`protected string $sourceConfigPath = '';`__

Path to the directory which contains config files to install into the projet config directory

__`protected array $configFilesList = [];`__

List of config file(s) to copy into the module config directory of the project

__`protected bool|string|array $sourceInstallScript = '';`__

Script to run for a specific installation of the module

__`protected string $targetSrcPath = '';`__

Path to the directory where the module will be installed

__`protected string $targetConfigPath = '';`__

Path to the directory where config files will be installed

## Methods

`self public __construct(string $modulePath)`

Take for argument the path of module to install (not the path where the module will be install) and keep it on property `$sourcePath`.

Also define the property `$projectPath` with the `ROOT_DIR` constant value.

### Getters

For more info about returned data, please refer to the explanation on the properties.

__`array public getConfigFilesList()`__

__`bool public getForceReinstall()`__

__`string public getName()`__

__`string public getProjectPath()`__

__`string public getSourceConfigPath()`__

__`bool|string|array public getSourceInstallScript()`__

__`string public getSourcePath()`__

__`string public getSourceSrcPath()`__

__`string public getTargetConfigPath()`__

__`string public getTargetSrcPath()`__

### Load install info

__`void public loadInfos()`__

Call the method `findModuleName` and obtain info from `bfwModulesInfos.json` with the method `obtainInfosFromModule`.
After that, check the `srcPath` property in info with a call to the method `checkPropertySrcPath`.
And keep all info to the class properties.

Properties values :
* `sourceSrcPath` : the real path of concatenation between property `sourcePath` and info property `srcPath`.
* `sourceConfigPath` : the real path of concatenation between property `sourcePath` and info property `configPath`.<br>
If `configPath` is not declared, it uses the info property `srcPath`.
* `configFilesList` : the info property `configFiles` (forced to array)
* `sourceInstallScript` the info property `installScript`

If the path of `sourceSrcPath` or `sourceConfigPath` is not found, an exception will be thrown;
the exception code will be the constant `\BFW\Install\ModuleInstall::ERR_LOAD_PATH_NOT_EXIST`.

__`void protected findModuleName()`__

Search and define the module from the path of source file and keep the value on the property `$name`.

Also define properties :
* `targetSrcPath` with a concatenation between constant `MODULES_DIR` and property `$name`.
* `targetConfigPath` with a concatenation between constant `CONFIG_DIR` and property `$name`.

__`\stdClass protected obtainInfosFromModule()`__

Call the method `\BFW\Module::installInfos` with the property `sourcePath` for argument and return the value returned by this method.

__`bool protected checkPropertySrcPath(\stdClass $infos)`__

Check if the property `srcPath` exist into `$infos` and also check if this value is not empty.

If the property not exist, an exception will be thrown;
the exception code will be the constant `\BFW\Install\ModuleInstall::ERR_LOAD_NO_PROPERTY_SRCPATH`.

And if the property is empty, an exception will be thrown;
the exception code will be the constant `\BFW\Install\ModuleInstall::ERR_LOAD_EMPTY_PROPERTY_SRCPATH`.

### Execute the module installation

__`void public install(bool $reinstall)`__

Keep the value of `$reinstall` on the property `$forceReinstall`.

After that, display and log a message about the installation
and call methods `createSymbolicLink`, `copyConfigFiles` and `checkInstallScript` to execute the installation.

A module will never be reinstalled unless `$reinstall` value is `true`.

__`void protected createSymbolicLink()`__

Create a directory in `/app/modules` with the module name.
This directory will be a symlink to the path kept on property `$srcPath`.

If the reinstall is forced and if the directory already exists in `/app/modules`, the directory will be removed first.

If the deletion of the old directory (reinstall case) fail, an exception will be thrown;
the exception code will be the constant `\BFW\Install\ModuleInstall::ERR_REINSTALL_FAIL_UNLINK`.

If the creation of the new symlink fail, an exception will be thrown;
the exception code will be the constant `\BFW\Install\ModuleInstall::ERR_INSTALL_FAIL_SYMLINK`.

__`void protected copyConfigFiles()`__

Call the method `createConfigDirectory` to manage the creation (or not) of the dedicated directory in config directory.

After that, call the method `copyConfigFile` for the file `manifest.json`.
Next, read all declared config file and call the method `copyConfigFile` for each config file.

__`void protected createConfigDirectory()`__

Create the directory in `/app/config` with the module name.

If the reinstall is forced and if the directory already exists in `/app/config`, the directory will be removed first.

If the deletion of the old directory (reinstall case) fail, an exception will be thrown;
the exception code will be the constant `\BFW\Install\ModuleInstall::ERR_REINSTALL_FAIL_REMOVE_CONFIG_DIR`.

If the creation of the new symlink fail, an exception will be thrown;
the exception code will be the constant `\BFW\Install\ModuleInstall::ERR_FAIL_CREATE_CONFIG_DIR`.

__`bool protected removeRecursiveDirectory(string $dirPath)`__

Remove a directory and all subdirectory into it.

Note: this method is dedicated to remove config directory, so we consider that there will be little recursion in it.
It's why it's a simple recall of the method `removeRecursiveDirectory`.

__`void protected copyConfigFile(string $configFileName)`__

Copy a config file (which exist in `$sourceConfigPath`) into the module config directory.

If the reinstall is forced and if the file already exists in `/app/config`, the file will be removed first.

If the source file not exist into `$sourceConfigPath`, an exception will be thrown;
the exception code will be the constant `\BFW\Install\ModuleInstall::ERR_COPY_CONFIG_SRC_FILE_NOT_EXIST`.

If the copy of the file fail, an exception will be thrown;
the exception code will be the constant `\BFW\Install\ModuleInstall::ERR_COPY_CONFIG_FAIL`.

__`void protected checkInstallScript()`__

Check the property `$sourceInstallScript` to know if a personalised installation script has been declared.

If the value is empty or `false`, we consider there is no script to execute.<br>
If the value is `true`, we consider there is a script and force the name to be `runInstallModule.php`.

### Execute the personalised installation script

__`void public runInstallScript(string $scriptName)`__

Execute the personalised installation script `$scriptName`.
The argument should not contain the path.
The path will always be the value of property `$sourcePath`.

The call to `require` is in the method, so the script will have access to `$this` and all properties.

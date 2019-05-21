# Install\ModuleManager\ModuleInfo

This class contains all info declared into the file `bfwModulesInfos.json` for a specific module.

## Properties

__`protected array|object $info;`__

Info extracted from file `bfwModulesInfos.json` (json_decode)

__`protected \bultonFr\Utils\Files\FileManager $fileManager;`__

The FileManager instance used to do action on files.

__`protected string $srcPath = '';`__

The src path into the module.

__`protected array $configFiles = [];`__

The list of all config files declared.  
If the value of $info is not an array, it will be converted.

__`protected string $configPath = '';`__

The path to config files

__`protected string $installScript = '';`__

The path to the install script.  
If the value of $info is not a string, it will be converted.

## Methods

`self public __construct(array|object $moduleInfo)`

Take for argument the return of json_decode of file `bfwModulesInfos.json`, and keep it on property `$info`.

With data into `$info`, the script populate properties `srcPath`, `configFiles`, `configPath` and `installScript` with the value of these properties into `$info`.  
If `$info` contain other properties, they will be added on the fly (so public) to the class instance.

### Getters

For more info about returned data, please refer to the explanation on the properties.

__`array|object public getInfo()`__

__`string public getSrcPath()`__

__`array getConfigFiles()`__

__`string getConfigPath()`__

__`string getInstallScript()`__

### Convert values

__`void protected convertValues()`__

Call data converter methods `convertConfigFiles` and `convertInstallScript`.

__`void protected convertConfigFiles()`__

Convert the property value `$configFiles` to be an array.  
If the value is not a string, it will be converted to an empty array.  
If the value is a string, it will be the first value of the array.

__`void protected convertInstallScript()`__

Convert the property value `$configFiles` to be a string.  
If the value is `true`, it will be converted to `runInstallModule.php`. Else if the value is not a string, it will be converted to an empty string.

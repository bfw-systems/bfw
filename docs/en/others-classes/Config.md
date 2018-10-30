# Config

Load and read all config file into a directory and makes available the values.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_JSON_PARSE`__

Exception code if the parse of a json file fail.

__`ERR_GETVALUE_FILE_NOT_INDICATED`__

Exception code if the file to use is not indicated into the method `getValue`.
(only if there are many config files)

__`ERR_FILE_NOT_FOUND`__

Exception code if the file to use is not found.

__`ERR_KEY_NOT_FOUND`__

Exception code if the asked config key not exist.

__`ERR_KEY_NOT_ADDED`__

Exception code if the key cannot be added to the config.

## Properties

__`protected string $configDirName = '';`__

Directory's name in config dir.

__`protected string $configDir = '';`__

The complete path of the readed directory.

__`protected string[] $configFiles = [];`__

The list of files to read or readed.

__`protected array $config = [];`__

The list of config values.

The key is the filename where is the config, and the value is an array with all values.

## Methods

__`self public __construct(string $configDirName)`__

Define the property `$configDirName` with the value of the parameter.
And also define the property `$configDir` with the full path to the directory.

The full path is defined by the concatenation of `CONFIG_DIR` and `$configDirName`.

### Getters

For more info about returned data, please refer to the explanation on the properties.

__`array public getConfig()`__

__`string public getConfigDir()`__

__`string public getConfigDirName()`__

__`string[] public getConfigFiles()`__

### Obtain or modify all config keys for a file

__`mixed public getConfigByFilename(string $filename)`__

Obtain all config keys in the file `$filename`.

__`self public setConfigForFilename(string $filename, array $config)`__

Modify all config keys for the file `$filename` with the new value `$config`.

If the file not exist before, it will be added to the list on the property `$configFiles`.

Keep in mind that will never change values into the file.

### Obtain or modify a specific config key

__`mixed public getValue(string $key, [string $file=null])`__

Obtain the value of the config key `$key`.

If there is only one config file loaded, the parameter `$file` can be `null`.
But if there are many config file loaded, you must indicate the name of the file.

If the parameter `$file` is `null` when many config file is loaded, an exception will be thrown;
the exception code will be the constant `\BFW\Config::ERR_GETVALUE_FILE_NOT_INDICATED`.

If the config file `$file` not exist, an exception will be thrown;
the exception code will be the constant `\BFW\Config::ERR_FILE_NOT_FOUND`.

If the config key `$key` not exist in the file `$file`, an exception will be thrown;
the exception code will be the constant `\BFW\Config::ERR_KEY_NOT_FOUND`.

__`self public setConfigKeyForFilename(string $filename, string $configKey, mixed $configValue)`__

Modify the key `$configKey` in the file `$filename` with the new value `$configValue`.

If the file not exist before, it will be added to the list on the property `$configFiles`.

If the key `$configKey` not existed before for the file, it will be added.

Keep in mind that will never change values into the file.

### Load config files

__`void public loadFiles()`__

Call the method `searchAllConfigFiles` to obtain the list of all config file to load.
And call the method `loadConfigFile` for each file found.

Note : The file `manifest.json` will always be ignored.

__`void protected searchAllConfigFiles(string $dirPath, [string $pathIntoFirstDir=""])`__

Search all file in the directory `$dirPath`.

If the item found is a directory (for a subdirectory), the method will recall itself with the second parameter `$pathIntoFisrtDir`.
This parameter contains the path the readed directory for the first call to this method.

The file `manifest.json` will be ignored by the reader.

__`void protected loadConfigFile(string $fileKey, string $filePath)`__

Obtain the file extension and call the method dedicated to the extension :
* json : `loadJsonConfigFile`
* php : `loadPhpConfigFile`

Note : Maybe yaml will be added someday...

__`void protected loadJsonConfigFile(string $fileKey, string $filePath)`__

Read the file `$filePath` with the function [json_decode](http://php.net/manual/en/function.json-decode.php)
and keep the value returned by the function on the property `$config` with the array key `$fileKey`.

The value of `$filePath` is the full path of the file, and the usual value of `$fileKey` is the filename.

If the value return by `json_decode` is null (for a decode error), an exception will be thrown;
the exception code will be the constant `\BFW\Config::ERR_JSON_PARSE`.

__`void protected loadPhpConfigFile(string $fileKey, string $filePath)`__

Use the method [require](http://php.net/manual/en/function.require.php) to include the file `$filePath`.
For php file, config file must return an array which is the value to keep on the property `$config` with the array key `$fileKey`.

Note : The file is included in the scope of the method, so the config file have access to `$this` of this class.

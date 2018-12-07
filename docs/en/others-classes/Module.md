# Module

This class is used to manage a module.
In the normal use of the framework, there is one instance of this class per module.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_FILE_NOT_FOUND`__

Exception code if the file is not found.

__`ERR_JSON_PARSE`__

Exception code if the parse of a json file fail.

__`ERR_RUNNER_FILE_NOT_FOUND`__

Exception code if the runner file to execute is not found.

## Properties

__`protected string $name = '';`__

The module's name

__`protected \BFW\Config|null $config;`__

The Config object for this module

__`protected \stdClass|null $loadInfos;`__

All information about how to run the module extract from the file `module.json`.

__`protected object $status;`__

An anonymous class with the load and run status.
The class contain public boolean properties `load` and `run`.

## Methods

__`self public __construct(string $name)`__

Define the property `$name` with the parameter value, and define the property `$status` with an anonymous class.

### Getters

For more info about returned data, please refer to the explanation on the properties.

__`\BFW\Config|null public getConfig()`__

__`\stdClass|null public getLoadInfos()`__

__`string public getName()`__

__`object public getStatus()`__

### Status info

__`bool public isLoaded()`__

Return the value of the property `$load` from the anonymous class in `$status` property.

__`bool public isRun()`__

Return the value of the property `$run` from the anonymous class in `$status` property.

### Load or obtain module info

__`void public loadModule()`__

Execute the loading of the module.

Call the method `loadConfig` to have a `\BFW\Config` for the module and call the method `obtainLoadInfos` to have all info about the module.

After that, change the value of the property `$load` on the anonymous class in `$status` property to `true`.

__`void protected loadConfig()`__

Check if there is a config directory for the module, and if so,
instantiate a `\BFW\Config` object with the path of the config directory for this module.
The instance is kept on the property `$config`.

After instantiating the `\BFW\Config` object, call this method `loadFiles` to load all module config file.

__`void protected obtainLoadInfos()`__

Obtain data declared on the file `module.json`.

All obtained data is parsed by the function [json_decode](http://php.net/manual/en/function.json-decode.php),
and after that, kept on the property `$loadInfos`.

__`\stdClass public static installInfos(string $sourceFiles)`__

Obtain data declared on the file `bfwModuleInfos.json` and parse them
with the function [json_decode](http://php.net/manual/en/function.json-decode.php)
before returning them.

__`mixed protected static readJsonFile(string $jsonFilePath)`__

Read, parse (with [json_decode](http://php.net/manual/en/function.json-decode.php)) a json file and return parsed data.

If the file not exist, an exception will be thrown;
the exception code will be the constant `\BFW\Module::ERR_FILE_NOT_FOUND`.

If the parse has failed, an exception will be thrown;
the exception code will be the constant `\BFW\Module::ERR_JSON_PARSE`.

### Manage dependency

__`self public addDependency(string $dependencyName)`__

Add the module `$dependencyName` in the `require` (loadInfos property) list of this module.

This is used by the property `needMe` (from loadInfos).
For more detail about that, please refer to the [issue #70](https://github.com/bulton-fr/bfw/issues/70).

### Execute the module

__`void public runModule()`__

If the module has not been already running (from property `$status`), create a closure to execute the runner file, and execute it.
Because the runner is executed from here, the runner file have access to `$this`.

__`string protected obtainRunnerFile()`__

Return the value of the property `runner` (from loadInfos) and check if the file exists.

If the runner file not exist, an exception will be thrown;
the exception code will be the constant `\BFW\Module::ERR_RUNNER_FILE_NOT_FOUND`.

# Monolog

This class create a bridge between [Monolog](https://github.com/Seldaek/monolog) and bfw monolog config file.

With a specific config format, this class will instantiate the Monolog Logger and load defined handlers on the logger.

The config format must be an array where each value is also an array with keys 
* `name` : The handlers class name (with namespace)
* `args` : An array with all arguments passed to the handler constructor

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_HANDLERS_LIST_FORMAT`__

Exception code if config for handlers list have not a correct format.

__`ERR_HANDLER_INFOS_MISSING_NAME`__

Exception code if a handler not have declared name.

__`ERR_HANDLER_NAME_NOT_A_STRING`__

Exception code if a handler name value is not a string.

__`ERR_HANDLER_CLASS_NOT_FOUND`__

Exception code if a handler class has not been found.

## Properties

__`protected string $channelName = '';`__

The logger channel name.

__`protected \BFW\Config $config;`__

The config object containing the handlers list.

__`protected \Monolog\Logger $logger;`__

The Monolog logger object.

__`protected array $handlers = [];`__

List of all declared handlers.

## Methods

__`self public __construct(string $channelName, \BFW\Config $config)`__

Kept the first parameter on the property `$channelName` and the second parameter on the property `$config`.

After that, instantiate the Monolog Logger and pass the property `$channelName` to the constructor.

### Getters

For more info about returned data, please refer to the explanation on the properties.

__`string public getChannelName()`__

__`\BFW\Config public getConfig()`__

__`array public getHandlers()`__

__`\Monolog\Logger public getLogger()`__

### Add a handler

__`void public addAllHandlers([string $configKeyName="handlers", [string $configFileName="monolog.php"]])`__

Read all handlers declared on the config key `$configKeyName` in the config file `$configFileName`,
and for each, call the method `addNewHandler` to instantiate and add him to the Monolog Logger.

If the config value is not an array, an exception will be thrown;
the exception code will be the constant `\BFW\Monolog::ERR_HANDLERS_LIST_FORMAT`.

__`void public addNewHandler(array $handlerInfos)`__

Check `$handlerInfos` with a call to the method `checkHandlerInfos`,
next instantiate the handler and add it to the list on the property `$handlers` before call the method `pushHandler` of the logger to add him.

### Check the handler info

__`void protected checkHandlerInfos(array $handlerInfos)`__

Only a call to methods `checkHandlerName` and `checkHandlerArgs`.

__`void protected checkHandlerName(array $handlerInfos)`__

Some check about the key `name` on the `$handlerInfos` array.

If the key `name` not exist on the array, an exception will be thrown;
the exception code will be the constant `\BFW\Monolog::ERR_HANDLER_INFOS_MISSING_NAME`.

If the value of the key `name` is not a string, an exception will be thrown;
the exception code will be the constant `\BFW\Monolog::ERR_HANDLER_NAME_NOT_A_STRING`.

If the value of the key `name` is a non-existing class, an exception will be thrown;
the exception code will be the constant `\BFW\Monolog::ERR_HANDLER_CLASS_NOT_FOUND`.

__`void protected checkHandlerArgs(array &$handlerInfos)`__

Some check about the key `args` on the `$handlerInfos` array.

If the key `args` not exist on the array, the key is added with an empty array for value.

If the value of the key `args` is not an array, the value will be an array with the old value on the key `0`.

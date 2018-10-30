# Core\Errors

This class catch all errors and exceptions by defining new render in handler functions.
Used renders are defined into config file `/app/config/bfw/errors.php`.

## Methods

### Define new handlers

__`self public __construct()`__

Call methods `defineErrorHandler()` and `defineExceptionHandler()` to define new handlers.

__`void protected defineErrorHandler()`__

Call the method `obtainErrorRender` to know if a new handler should be defined.

If a new handler should be used, call the function [set_error_handler](http://php.net/manual/en/function.set-error-handler.php)
and define the method `errorHandler` as new handler.

__`void protected defineExceptionHandler()`__

Call the method `obtainExceptionRender` to know if a new handler should be defined.

If a new handler should be used, call the function [set_exception_handler](http://php.net/manual/en/function.set-exception-handler.php)
and define the method `exceptionHandler` as new handler.

### Know the render to use

__`bool|array protected obtainErrorRender()`__

Obtain the value of the config key `errorRenderFct` from config file `/app/config/bfw/errors.php`
and return the value returned by method `defineRenderToUse`.

__`bool|array protected obtainExceptionRender()`__

Obtain the value of the config key `exceptionRenderFct` from config file `/app/config/bfw/errors.php`
and return the value returned by method `defineRenderToUse`.

__`bool|array protected defineRenderToUse(array $renderConfig)`__

This method extract some data from `$renderConfig` (which is the render info define in config file)
to know if a render should be used and his name.

First we check if the render should be used (key `enabled`).<br>
Next we return the render to use for the current execution mode (cli or www).

If no render is found or if it's disabled, the method will return `false`.
Else, we return the render to use.

### When an error or an exception is caught

__`void public errorHandler(int $errSeverity, string $errMsg, string $errFile, int $errLine)`__

This is the new handler defined by `defineErrorHandler` and called by PHP when an error is thrown.

For more detail about arguments, please refer to the php doc for [set_error_handler](http://php.net/manual/en/function.set-error-handler.php).

This method will call method `obtainErrorRender` to know the render to use.
It also uses the method `obtainErrorType` to have a string with the error type (from integer `$errSeverity`) for the displayed message.

And it calls the method `callRender` to call the render to use to display the error.

__`void public exceptionHandler(\Throwable $exception)`__

This is the new handler defined by `defineExceptionHandler` and called by PHP when an error is thrown.

For more detail about arguments, please refer to the php doc for [set_exception_handler](http://php.net/manual/en/function.set-exception-handler.php).

This method will call method `obtainExceptionRender` to know the render to use.
And it calls the method `callRender` to call the render to use to display the error.

__`void protected callRender(array $renderInfos, string $errType, string $errMsg, string $errFile, int $errLine, array $backtrace, [int|null $exceptionCode=null])`__

Call the method `saveIntoPhpLog` to be sure the error will be in the log.
And after that, call render defined on `$renderInfos` with all other arguments of this method for argument (in the same order).

### Save in the php log

__`void protected saveIntoPhpLog(string $errType, string $errMsg, string $errFile, int $errLine)`__

Called by `callRender`, this method will log the error in the php error log.

This method exists because when we define new handlers, sometimes errors are never written in the log.
So this method exists to always be sure errors are into the log.

The message is : Error detected : `$errType` `$errMsg` at `$errFile`:`$errLine`

### Obtain the string error type

`string protected obtainErrorType(int $errSeverity)`

When php call handler, it sent an integer `$errSeverity` for error type which is a PHP constant.
For displayed message, we prefer to read a string to know the error type.
This message convert the `$errSeverity` integer to the error type string.

The conversion map is :
* `E_ERROR` : Fatal
* `E_CORE_ERROR` : Fatal
* `E_USER_ERROR` : Fatal
* `E_COMPILE_ERROR` : Fatal
* `E_RECOVERABLE_ERROR` : Fatal
* `E_WARNING` : Warning
* `E_CORE_WARNING` : Warning
* `E_USER_WARNING` : Warning
* `E_COMPILE_WARNING` : Warning
* `E_PARSE` : Parse
* `E_NOTICE` : Notice
* `E_USER_NOTICE` : Notice
* `E_STRICT` : Strict
* `E_DEPRECATED` : Deprecated
* `E_USER_DEPRECATED` : Deprecated

For anything else, the returned string will be `Unknown`.

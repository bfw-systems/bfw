# Core\Cli

This class search the cli file to execute in argument, check the asked file and execute it if it's ok.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_NO_FILE_SPECIFIED_IN_ARG`__

Exception code if the cli file to execute is not specified with the "-f" argument.

__`ERR_FILE_NOT_FOUND`__

Exception code if the cli file to execute is not found.

## Property

__`protected string $executedFile = '';`__

The name of the executed cli file

## Methods

### Getter

__`string public getExecutedFile()`__

Return the value of the property `$executedFile`.

### Search the file to execute

__`string public obtainFileFromArg()`__

Use the [getopt](http://php.net/manual/en/function.getopt.php) function to define the `-f` argument.
This argument is mandatory and this value should be the cli file to execute.

If the `-f` is missing, an exception will be thrown;
the exception code will be the constant `\BFW\Core\Cli::ERR_NO_FILE_SPECIFIED_IN_ARG`.

The returned value is the path to the asked cli file in argument.
The extension `.php` is added by this method, so the extension should not be into the `-f` argument value.

### Check the file

__`bool protected checkFile()`__

Run some check on the file to execute.
For the moment, we only check if the file exists.

The returned value is the check status.

But if the file is not found, an exception will be thrown;
the exception code will be the constant `\BFW\Core\Cli::ERR_FILE_NOT_FOUND`.

### Execute the file

__`void public run(string $file)`__

This method keep the file to execute (with this path) `$file` to the property `$executedFile`,
and call methods `checkFile` and `execFile` (if the check is ok).

In the usual execution of the system, the `$file` value is the value returned by the method `obtainFileFromArg`.

__`void protected execFile()`__

Execute the cli file in a closure, so it will have his own scope.

Note: The cli file will have access to the `$this` of this class.

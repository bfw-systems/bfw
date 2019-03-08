# Core\ErrorsDisplay

This class completes the class [Errors](./Errors.php).
Errors class catch errors and exception, this class display it to the user.

With the default config file `/app/config/bfw/errors.php`, renders defined are methods of this class.

## Methods

### Cli render

__`void public static defaultCliErrorRender(string $errType, string $errMsg, string $errFile, int $errLine, array $backtrace, int|null $exceptionCode)`__

Called for an error or an exception (not caught), only if the application is run by cli.

This method will display a white error message on a red background, and will kill the script.
The backtrace is not displayed.

#### Example

For example, I have install the bfw-cli module, edit the file `src/cli/exemple.php` and add the line `throw new \Exception('Test');`.

Note : The line "Error detected..." is the line which is normally written in the php log.
It's my local config of php which write the line in the output.

![](https://projects.bulton.fr/bfw/wiki/img/v3.0/ScreenErrorsDisplayCli.png)

### www render

__`void public static defaultErrorRender(string $errType, string $errMsg, string $errFile, int $errLine, array $backtrace, int|null $exceptionCode)`__

Called for an error or an exception (not caught), only if the application is not run by cli.

This method will display the error message with the backtrace, and will kill the script.

#### Example

I have taken the example doing in the doc page [example-scripts](../get-started/example-scripts.md#web-script).
In the controller, I have added the line `throw new \Exception('Test');` and the end of the method `index`.

Into the php log I have : `[Fri Aug 24 21:23:14 2018] Error detected : Exception Uncaught Test at /opt/dev/bfw/test/src/controllers/Test.php:10`

And in the browser :

![](https://projects.bulton.fr/bfw/wiki/img/v3.0/ScreenErrorsDisplayWeb.png)

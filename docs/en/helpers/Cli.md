# Helpers\Cli

This helper give methods to write text into a shell with color and/or style.

To write a message, you can use methods `displayMsg` and `displayMsgNL`.
The first write a message without a line break. The second adds a line break automatically at the end of the message.

## Flush system

By default, when the framework is loaded, the function [ob_start](http://php.net/manual/en/function.ob-start.php) is called.
Because of that, all output is sent into a buffer and displayed when application shutdown.
To avoid that, you can force the buffer to display with the function [ob_flush](http://php.net/manual/en/function.ob-flush.php).

In cli, it can be useful to always display the buffer content when we write a message.
But sometimes, we can prefer the message is not displayed right now.

To manage this two case, a flush system is integrated.
By default the buffer is flushed at the end of methods `displayMsg` and `displayMsgNL`.
But you can define to not flush and choose when you want to flush the buffer.

For doing that, there are :
* constants :
  * `const FLUSH_AUTO = 'auto';`
  * `const FLUSH_MANUAL = 'manual';`
* property `public static $callObFlush = self::FLUSH_AUTO;`

It's the property value who defines if the flush will be automatically called or not.
If the value is the constant `FLUSH_AUTO`, the flush will be done at the end of methods.
But if the value is the constant `FLUSH_MANUAL`, the flush will never be done by the system; you should do it yourself.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_COLOR_NOT_AVAILABLE`__

Exception code if the color is not available.

__`ERR_STYLE_NOT_AVAILABLE`__

Exception code if the style is not available.

## Methods

### To display a message

__`public static function displayMsg(string $msg, string $colorTxt = 'white', string $style = 'normal', string $colorBg = 'black')`__

__`public static function displayMsgNL(string $msg, string $colorTxt = 'white', string $style = 'normal', string $colorBg = 'black')`__

These two methods will display a message with color and/or style.

The first method will just display the message, the second will add a line break (`\n`) at the end of the message.

Arguments are :
* `string $msg` : It's the message to display.
* `string $colorTxt` : It's the color of the text. Refer to the method `colorForShell` to know available color.
* `string $style` : It's the style of the text. Refer to the method `styleForShell` to know available color.
* `string $colorBg` : It's the background color to use. Refer to the method `colorForShell` to know available color.

If there is only the first argument, the color and style will not be defined and stay with shell configuration at this moment.
And if there are only the first three arguments, the background-color will not be defined and stay with shell configuration at this moment.

### To define color code to use in the shell

__`protected static function colorForShell(string $color, string $type): int`__

This method will return the color code to use in the shell for a string color name.

Available colors are :
* black
* red
* green
* yellow
* blue
* magenta
* cyan
* white

If the color name into the argument not exist, an exception will be thrown.
The exception code will be the constant `\BFW\Helpers\Cli::ERR_COLOR_NOT_AVAILABLE`.

The argument `string $type` is where the color code will be used.
The value can be `txt` or `bg`.

It's to return the correct color code.
To explain, each color has an integer value.
But if the color is for the text color, the value should be between 30 and 39.
And if the color is for the background color, the value should be between 40 and 49.

So we ask where the color will be used to return to the correct value range.

### To define style code to use in the shell

__`protected static function styleForShell(string $style): int`__

This method will return the style code to use in the shell for a string style name.

Available styles are :
* normal
* bold
* not-bold
* underline
* not-underline
* blink
* not-blink
* reverse
* not-reverse

if the style name into the argument not exist, an exception will be thrown.
The exception code will be the constant `\BFW\Helpers\Cli::ERR_STYLE_NOT_AVAILABLE`.

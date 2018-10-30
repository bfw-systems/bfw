# Options

This class manage an option list to compare and complete options from the user to default option.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_KEY_NOT_EXIST`__

Exception code if a key not exist.

## Property

__`protected array $options = [];`__

List of all options with their values.

## Methods

`self public __construct(array $defaultOptions, array $options)`

Use the function [array_merge](http://php.net/manual/en/function.array-merge.php)
to override values in `$defaultOptions` by existing values in `$options`.

Kept the value returned by array_merge on the property `$options`.

### Getter

__`array public getOptions()`__

Return the value of the property `$options`, so the final options array.

__`mixed public getValue(string $optionKey)`__

Return the option value for the key `$optionKey`.

If the key not exist, an exception will be thrown;
the exception code will be the constant `\BFW\Options::ERR_KEY_NOT_EXIST`.

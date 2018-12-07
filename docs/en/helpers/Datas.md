# Helpers\Datas

This helper gives methods to manage data.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_CHECKTYPE_INFOS_FORMAT`__

Exception code if the format of the info passed to checkType method is not correct.

__`ERR_CHECKTYPE_DATA_OR_TYPE_VALUE_FORMAT`__

Exception code if data or type used to check the variable does not have a right value.

## Methods

## Check data

__`bool public static checkMail(string $mail)`__

This method check if the data into `$mail` is an email address or not.

__`bool public static checkType(array $vars)`__

This method check if a value has the right type.

This method has been added because when you have many (like 10 for example) vars types to check,
it's easier to generate an array and call this method.
However I agree, when you have one or two data types to check, use a simple `if` is better ;)

To know accepted value for the type, please refer you to return value of the function [gettype](http://php.net/manual/en/function.gettype.php).
Some type is auto-converted by the method `checkType` to have the same value as the value returned by gettype.
The type `int` becomes `integer`, and the type `float` becomes `double`.


`$vars` should have a specific format :
```php
array(
    array(
        'type' => 'myType',
        'data' => 'myData'
    ),
    array(...)
...)
```

For example :
```php
\BFW\Helpers\Datas::checkType([
    [
        'type' => 'int',
        'data' => 42
    ],
    [
        'type' => 'string',
        'data' => 'foo-bar'
    ]
]);
```

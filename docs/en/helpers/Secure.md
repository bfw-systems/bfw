# Helpers\Secure

This helper gives methods to secure your data.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_SECURE_KNOWN_TYPE_FILTER_NOT_MANAGED`__

Exception code if the data type is not managed in the method to secure known type.

__`ERR_SECURE_ARRAY_KEY_NOT_EXIST`__

If the asked key not exist in the array to secure.

## Methods

### Hash a string

__`string public static hash(string $val)`__

Return a hashed string with a combination of md5 and sha256.

**Please, not use that for your passwords.**
For passwords, since PHP 5.5, there are dedicated functions
[password_hash](http://php.net/manual/en/function.password-hash.php) and
[password_verify](http://php.net/manual/en/function.password-verify.php).

### Secure system

__`mixed public static secureKnownType(mixed $data, string $type)`__

Use the function [filter_var](http://php.net/manual/fr/function.filter-var.php) to secure data.

Only some type is managed. If the type is not, an exception will be thrown.<br>
The available list types with the filter used is :
* `int` or `integer` : `FILTER_VALIDATE_INT`
* `float` or `double` : `FILTER_VALIDATE_FLOAT`
* `bool` or `boolean` : `FILTER_VALIDATE_BOOLEAN`
* `email` : `FILTER_VALIDATE_EMAIL`

__`mixed public static secureUnknownType(mixed $data, bool $htmlentities)`__

Secures a datum in the idea that it's a string.

Used filters are (in order) :
* secure function from the database module; or if not declared, the [addslashes](http://php.net/manual/en/function.addslashes.php) function.
* If the argument `$htmlentities` is equal to `true`, the function [htmlentities](http://php.net/manual/fr/function.htmlentities.php).

__`null|string public static getSqlSecureMethod()`__

To obtain the name of the secure function from the database module.
This function should be declared into the config file `app/config/bfw/global.php`.

If no function is declared or if the function is not callable, the `null` value will be returned.

__`mixed public static secureData(mixed $data, string $type, bool $htmlentities)`__

Secures a data with methods `secureKnownType` and `secureUnknownType`.

If `$datas` is an array, the array will be read and each key and value will be secure.

For arguments `$type`, please refer to the explanation of the method `secureKnownType`.<br>
For arguments `$htmlentities`, please refer to the explanation of the method `secureUnknownType`.<br>

## Secure with an array

__`mixed public static getSecureKeyInArray(array &$array, string $key, string $type, [bool $htmlentities=false])`__

To obtain the secure value for the key `$key` in the array `$array`.
The value is secure by the method `secureData`.

If the key not exist, an exception will be thrown.
The exception code will be the constant `\BFW\Helpers\Secure::ERR_SECURE_ARRAY_KEY_NOT_EXIST`.

For arguments `$type`, please refer to the explanation of the method `secureKnownType`.<br>
For arguments `$htmlentities`, please refer to the explanation of the method `secureUnknownType`.<br>

__`array public static getManySecureKeys(array &$arraySrc, array $keysList, [bool $throwOnError=true])`__

To secure many keys into an array.

Because methods `getSecureKeyInArray` (to obtain one data) will throw an exception if the key not exist,
you can sometimes have many try/catch one after the other.

The argument `$keyList` is an array with the list of all keys to obtain.
The array keys are keys you want to obtain, and the value can be an array or a string.
If it's an array, it should have keys `type` for the data type and `htmlentities` to know if the method htmlentities should be used.
If it's a string, it should be the data type to have (in this case, the function htmlentities will not be used).<br>
For keys `type`, please refer to the explanation of the method `secureKnownType`.<br>
For keys `htmlentities`, please refer to the explanation of the method `secureUnknownType`.<br>

The argument `$throwOnError` define if an exception should be thrown if one asked key is not found.
By default, the value is `true` so an exception will be thrown.
The exception code will be the constant `\BFW\Helpers\Secure::ERR_OBTAIN_KEY`.<br>
If the value of `$throwOnError` is false, the value of all missing keys will be `null`.

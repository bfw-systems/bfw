# Helpers\Http

This helper gives methods to doing HTTP redirection and obtain securised data from HTTP request.

## Doing redirection

__`void public static redirect(string $page, [bool $permanent=false, [bool $callExit=false]])`__

This method will send HTTP header to create a redirection to the url `$page`.
By default, the HTTP code is 302 (not permanent), but if you want a 301 (permanent), the value of `$permanent` should be `true`.
And if you want to stop all the script at the method call,
the value of `$callExit` should be `true` to call the [exit](http://php.net/manual/en/function.exit.php) function.

## Obtain secured data from HTTP request

### Security system

__`string protected static getSecureHelpersName()`__

To secure data, by default the system uses the helper `\BFW\Helpers\Secure`.
You can change the used helper with an override of this method.

However if you cannot override other methods, I recommend using a helper
which extends the default used helper not to have the "unknown method" error ;)

### Obtain one data

__`mixed public static obtainGetKey(string $key, string $type, [bool $htmlentities=false])`__<br>
__`mixed public static obtainPostKey(string $key, string $type, [bool $htmlentities=false])`__

The method `obtainGetKey` will return you the securised `$key` key from GET data;
and the method `obtainPostKey` will return you the securised `$key` key from POST data.

If the key not exist, an exception will be thrown by secure system.
The exception code will be the constant `\BFW\Helpers\Secure::ERR_SECURE_ARRAY_KEY_NOT_EXIST`.

The `$type` argument is to define the type of data you should have. It's for better security.

With this type, the secure method (`\BFW\Helpers\Secure::securiseKnownTypes`)
will use the function [filter_var](http://php.net/manual/en/function.filter-var.php) :
* `int` or `integer`
* `float` or `double`
* `bool` or `boolean`
* `email`

For any others value, the system will consider that it's a string and use the secure method `\BFW\Helpers\Secure::securiseUnknownType`.
The argument `$htmlentities` it's only used in this case.
If this value is `true`, the function [htmlentities](http://php.net/manual/en/function.htmlentities.php) will also be used to secure data.

For more info about the secure system, please refer to the [dedicated page for Secure helpers](./Secure.md).

### Obtain many data

__`array public static obtainManyGetKeys(array $keysList, [bool $throwOnError=true])`__<br>
__`array public static obtainManyPostKeys(array $keysList, [bool $throwOnError=true])`__

Because methods to obtain one data will throw an exception if the key not exist, you can sometimes have many try/catch one after the other.
To avoid that, there are these two methods.

The argument `$keyList` is an array with the list of all keys to obtain.
The array keys are keys you want to obtain, and the value can be an array or a string.
If it's an array, it should have keys `type` for the data type and `htmlentities` to know if the method htmlentities should be used.
If it's a string, it should be the data type to have (in this case, the function htmlentities will not be used).

I will not explain again about "type" and "htmlentities", please refer to the part "Obtain one data" just before.

The argument `$throwOnError` define if an exception should be thrown if one asked key is not found.
By default, the value is `true` so an exception will be thrown.
The exception code will be the constant `\BFW\Helpers\Secure::ERR_OBTAIN_KEY`.<br>
If the value of `$throwOnError` is false, the value of all missing keys will be `null`.

# Request

This class detect and kept a lot of info about the current user and request.

The [design pattern Singleton](https://en.wikipedia.org/wiki/Singleton_pattern) is used.
But because an issue ([#84](https://github.com/bulton-fr/bfw/issues/84)) with unit test, the constructor is actually public.
This will be changed to protected when a solution will be found and implemented.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_KEY_NOT_EXIST`__

Exception code if a key not exist into the `$_SERVER` array.

## Properties

__`protected static \BFW\Request $instance = null;`__

The instance of this class (singleton pattern)

__`protected string $ip = '';`__

The user IP

__`protected string $lang = '';`__

The client preferred language.
For example, the value will be "en" if the user declares "en-US" for preferred language.

__`protected string $referer = '';`__

The referer url

__`protected string $method = '';`__

The HTTP method (GET/POST/PUT/DELETE/...)

__`protected boolean|null $ssl;`__

If the request is with ssl (https) or not

__`protected \stdClass|null $request;`__

The current request.

It's an object with all properties returned by the function [parse_url](http://php.net/manual/en/function.parse-url.php).

## Methods

__`\BFW\Request public static getInstance()`__

Used by the Singleton pattern to instantiate the class and to always return the same instance.

### Getters

__`string public getIp()`__

__`string public getLang()`__

__`string public getMethod()`__

__`string public getReferer()`__

__`\stdClass|null public getRequest()`__

__`bool|null public getSsl()`__

### Value of $_SERVER

__`string protected serverValue(string $keyName)`__

Use the method `getServerValue` to obtain the value of the key `$keyName` in the `$_SERVER` array.

If the method `getServerValue` throw an exception, an empty string will be returned.

__`string public static getServerValue(string $keyName)`__

Return the value of the key `$keyName` in the array `$_SERVER`.

If the key `$keyName` not exist in the array, an exception will be thrown;
the exception code will be the constant `\BFW\Request::ERR_KEY_NOT_EXIST`.

### Detect info

__`void public runDetect()`__

Call all detection methods (in order) : 
* `detectIp`
* `detectLang`
* `detectReferer`
* `detectMethod`
* `detectSsl`
* `detectRequest`

__`void protected detectIp()`__

Detect the user IP address.

__`void protected detectLang()`__

Detect the preferred language of the user and format it.

For example, if the preferred language is "en-US", the value returned will be "en".

__`void protected detectMethod()`__

Detect the HTTP method.

__`void protected detectReferer()`__

Detect the referer url.

__`void protected detectRequest()`__

Detect many info about the current request with the function [parse_url](http://php.net/manual/en/function.parse-url.php).

__`void protected detectSsl()`__

Detect if ssl is used for the current request.

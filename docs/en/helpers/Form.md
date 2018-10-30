# Helpers\Form

This helper gives methods to secure your html forms again [CSRF attack](https://en.wikipedia.org/wiki/Cross-site_request_forgery).

You should instantiate this helper for each form you have.
You will give a token to add into your form (hidden input), and when you receive form data, you start by checking if token match.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_NO_TOKEN`__

Exception code if there is no token declared.

__`ERR_NO_TOKEN_FOR_FORM_ID`__

Exception code if there is no token for the form id.

__`ERR_FORM_ID_EMPTY`__

Exception code if the form id is not declared.

## Property

__`protected string $formId = '';`__

The form id for which the token will be created or checked.

## Methods

__`self public __construct(string $formId)`__

The constructor.
The argument `$formId` should contain the form id for which the token will be created or checked.

If `$formId` is empty, an exception will be thrown.
The exception code will be the constant `\BFW\Helpers\Form::ERR_FORM_ID_EMPTY`.

### Getter

__`string public function getFormId()`__

Return the value of property `$formId`.

### Create and save the token

__`string public createToken([int $expire=15])`__

Generate a new token for the form and return it.
The argument `$expire` is the time (in minutes) during which the token is valid.

At the end of the method, the method `saveToken` is called with an object for argument.
This argument contains three properties :
* `string $token` : The token value
* `\DateTime $date` : The create date of the token
* `int $expire` : The expire time (in minute)

__`void protected saveToken(object $saveInfos)`__

Save the token info.
By default, it saved into PHP Session by the call to `saveTokenInSession`.

To allow you to use different ways to save it, by default this method only contain the call to `saveTokenInSession` and do nothing else.

__`void protected saveTokenInSession(object $saveInfos)`__

Save the token and valid time into php session.

Into session array, token is saved on the key `formsTokens`.
The value of this key is an array where key is form id, and the value is the object `$saveInfos`.

### Know if a token exists

__`bool public hasToken()`__

Check if the form (declared for this instance) have a token declared.
This method also check if the token is still valid.

### Check the token

__`bool public checkToken(string $tokenToCheck)`__

Check the given token in argument match with the token in session.
The token's validity is also checked.

Because the internal call to `obtainToken`, if the token not exist, an exception will be thrown.
It's why I recommend calling the method `hasToken` before and call this method only if `hasToken` has returned `true`.

__`object protected obtainToken()`__

Get token info.
By default, it searches into PHP Session by the call to `obtainTokenFromSession`.

To allow you to use different ways to get it, by default this method only contain the call to `obtainTokenFromSession` and do nothing else.

__`object protected obtainTokenFromSession()`__

Get token info from PHP session.

An exception will be thrown if the token not exist.
The exception code will be the constant :
* `\BFW\Helpers\Form::ERR_NO_TOKEN` if the key `formsTokens` not exist into PHP session
* `\BFW\Helpers\Form::ERR_NO_TOKEN_FOR_FORM_ID` if the form id has not been found into the array on `formsTokens` key of PHP session.

## Example

Yes this code is not clean, but for a simple example, it's enought.

```php
<?php

$formToken   = new \BFW\Helpers\Form('auth');
$displayForm = true;
$formError   = false;

if (!empty($_POST)) {
    $login = \BFW\Helpers\Http::obtainPostKey('login', 'string');
    $pwd   = \BFW\Helpers\Http::obtainPostKey('pwd', 'string');
    $token = \BFW\Helpers\Http::obtainPostKey('token', 'string');
    
    $formError = true;
    
    if ($formToken->hasToken() === true) {
        if ($formToken->checkToken($token) === true) {
            if ($auth->login($login, $pwd) === true) {
                $displayForm = false;
                $formError   = false;
            }
        }
    }
}

if ($displayForm === true) {
    $token = $formToken->createToken();
    
    if ($formError === true) {
        echo '<p class="error">There is an error somewhere.</p>';
    }

    echo '
        <form id="auth">
            <input type="text" name="login" placeholder="Login" /><br />
            <input type="password" name="pwd" placeholder="Password" /><br />
            <input type="hidden" name="token" value="'.$token.'" />
            <input type="submit" value="Login" />
        </form>
    ';
}
```

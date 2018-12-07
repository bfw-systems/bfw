# Helpers\Constants

This helper give method to manage constants.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_ALREADY_DEFINED`__

Exception code if the constant is already defined.

## Method

__`void public static create(string $cstName, mixed $cstValue)`__

This method create the constant `$cstName` with the value `$cstValue`.
If the constant already exist, an exception will be thrown;
the exception code will be the constant `\BFW\Helpers\Constants::ERR_ALREADY_DEFINED`.

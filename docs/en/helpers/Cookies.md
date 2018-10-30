# Helpers\Cookies

This helper gives method to manage cookies.

## Method

__`void public static create(string $name, mixed $value, [int $expire=1209600])`__

This method create the cookie `$name` with the value `$value`.
You can define an expiration time with `$expire`; if not defined, the default expiration time is 14 days.

<?php

namespace BFW\Helpers;

/**
 * Helpers to manage cookies
 */
class Cookies
{
    /**
     * Create a cookie
     * 
     * @param string $name The cookie's name
     * @param mixed $value The cookie's value
     * @param integer $expire The cookie's expire time
     * 
     * @return void
     */
    public static function createCookie($name, $value, $expire = 1209600)
    {
        $expireTime = time() + $expire;
        setcookie($name, $value, $expireTime);
    }
}

<?php

namespace BFW\Helpers;

/**
 * Helpers to manage cookies
 */
class Cookies
{
    /**
     * @var string $path The path on the server in which the cookie will
     * be available on
     */
    public static $path = '/';
    
    /**
     * @var string $domain The (sub)domain that the cookie is available to.
     */
    public static $domain = '';
    
    /**
     * @var bool $httpOnly When TRUE the cookie will be made accessible only
     * through the HTTP protocol. This means that the cookie won't be
     * accessible by scripting languages, such as JavaScript.
     */
    public static $httpOnly = true;
    
    /**
     * @var bool $secure Indicates that the cookie should only be transmitted
     * over a secure HTTPS connection from the client.
     * 
     * Please change the value to false if your site not use HTTPS connection.
     */
    public static $secure = true;
    
    /**
     * @var string $sameSite Same-site cookies allow servers to mitigate the
     * risk of CSRF and information leakage attacks by asserting that a
     * particular cookie should only be sent with requests initiated
     * from the same registrable domain.
     * 
     * Value can only be "lax" or "strict".
     */
    public static $sameSite = 'lax';
    
    /**
     * Create a cookie
     * 
     * @param string $name The cookie's name
     * @param mixed $value The cookie's value
     * @param integer $expire The cookie's expire time
     * 
     * @return void
     */
    public static function create(string $name, $value, int $expire = 1209600)
    {
        $cookieText = 'Set-Cookie: '.$name.'='.$value;
        
        $expireTime = new \DateTime;
        $expireTime->modify('+ '.$expire.' second');
        $cookieText .= '; Expires='.$expireTime->format('D, d M Y H:i:s e');
        
        $cookieText .= '; Path='.self::$path;
        $cookieText .= '; Domain='.self::$domain;
        
        if (self::$httpOnly === true) {
            $cookieText .= '; HttpOnly';
        }
        
        if (self::$secure === true) {
            $cookieText .= '; Secure';
        }
        
        if (!empty(self::$sameSite)) {
            $cookieText .= '; Samesite='.self::$sameSite;
        }
        
        header($cookieText);
    }
}

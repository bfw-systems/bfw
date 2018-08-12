<?php

namespace BFW;

/**
 * Class to get informations about http request.
 * Singleton pattern.
 */
class Request
{
    /**
     * @const ERR_KEY_NOT_EXIST Exception code if a key not exist into the
     * $_SERVER array.
     */
    const ERR_KEY_NOT_EXIST = 1110001;
    
    /**
     * @var \BFW\Request $instance Instance for this class (singleton pattern)
     */
    protected static $instance = null;

    /**
     * @var string $ip The client IP
     */
    protected $ip = '';

    /**
     * @var string $lang The client primary language
     */
    protected $lang = '';

    /**
     * @var string $referer The referer url
     */
    protected $referer = '';

    /**
     * @var string $method The HTTP method (GET/POST/PUT/DELETE/...)
     */
    protected $method = '';

    /**
     * @var boolean|null $ssl If the request is with ssl (https) or not
     */
    protected $ssl;

    /**
     * @var \stdClass|null The current request
     */
    protected $request;

    /**
     * Create singleton instance for this class
     * 
     * @return \BFW\Request
     */
    public static function getInstance(): Request
    {
        if (self::$instance === null) {
            $calledClass    = get_called_class(); //Autorize extends this class
            self::$instance = new $calledClass;
        }

        return self::$instance;
    }

    /**
     * Get the client IP
     * 
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * Get the client primary language
     * 
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * Get the referer url
     * 
     * @return string
     */
    public function getReferer(): string
    {
        return $this->referer;
    }

    /**
     * Get the http method
     * 
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get information about if the request is ssl
     * 
     * @return boolean|null
     */
    public function getSsl()
    {
        return $this->ssl;
    }

    /**
     * Get the current request
     * 
     * @return \stdClass|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the information from the $_SERVER array if the key exist.
     * If not exist, return an exception.
     * 
     * @param string $keyName The key's value in $_SERVER array
     * 
     * @return string
     * 
     * @throws \Exception If the key not exist into $_SERVER
     */
    public static function getServerValue(string $keyName): string
    {
        if (!isset($_SERVER[$keyName])) {
            throw new \Exception(
                'The key '.$keyName.' not exist into $_SERVER array',
                self::ERR_KEY_NOT_EXIST
            );
        }

        return $_SERVER[$keyName];
    }
    
    /**
     * Get the information from the $_SERVER array if the key exist.
     * If not exist, return a empty string.
     * 
     * @param string $keyName The key's value in $_SERVER array
     * 
     * @return string
     */
    protected function serverValue(string $keyName): string
    {
        $calledClass = get_called_class(); //Autorize extends this class
        
        try {
            return $calledClass::getServerValue($keyName);
        } catch (\Exception $e) {
            return '';
        }
    }
    
    /**
     * Run all detect method
     * 
     * @return void
     */
    public function runDetect()
    {
        $this->detectIp();
        $this->detectLang();
        $this->detectReferer();
        $this->detectMethod();
        $this->detectSsl();
        $this->detectRequest();
    }

    /**
     * Detect the client IP
     * 
     * @return void
     */
    protected function detectIp()
    {
        $this->ip = $this->serverValue('REMOTE_ADDR');
    }

    /**
     * Detect the primary client's language
     * 
     * @return void
     */
    protected function detectLang()
    {
        /*
         * HTTP_ACCEPT_LANGUAGE -> fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4
         * First "fr-FR" (preference 1/1)
         * After in the order, "fr" (preference 0.8 / 1)
         * Next "en-US" (preference 0.6/1)
         * End "en" (preference 0.4/1)
         **/
        
        $acceptLanguage = $this->serverValue('HTTP_ACCEPT_LANGUAGE');
        if (empty($acceptLanguage)) {
            $this->lang = '';
            return;
        }
        
        $acceptedLangs = explode(',', $acceptLanguage);
        $firstLang     = explode(';', $acceptedLangs[0]);
        $lang          = strtolower($firstLang[0]);

        if (strpos($lang, '-') !== false) {
            $minLang = explode('-', $lang);
            $lang    = $minLang[0];
        }

        $this->lang = $lang;
    }

    /**
     * Detect the referer page
     * 
     * @return void
     */
    protected function detectReferer()
    {
        $this->referer = $this->serverValue('HTTP_REFERER');
    }

    /**
     * Detect the http method
     * 
     * @return void
     */
    protected function detectMethod()
    {
        $this->method = $this->serverValue('REQUEST_METHOD');
    }

    /**
     * Detect if the request is with ssl (https)
     * 
     * @return void
     */
    protected function detectSsl()
    {
        $serverHttps = $this->serverValue('HTTPS');
        $fwdProto    = $this->serverValue('HTTP_X_FORWARDED_PROTO');
        $fwdSsl      = $this->serverValue('HTTP_X_FORWARDED_SSL');

        $this->ssl = false;

        if (!empty($serverHttps) && $serverHttps !== 'off') {
            $this->ssl = true;
        } elseif (!empty($fwdProto) && $fwdProto === 'https') {
            $this->ssl = true;
        } elseif (!empty($fwdSsl) && $fwdSsl === 'on') {
            $this->ssl = true;
        }
    }

    /**
     * Detect the current request informations
     * 
     * @return void
     */
    protected function detectRequest()
    {
        $parseUrl = parse_url($this->serverValue('REQUEST_URI'));
        $scheme   = ($this->ssl === true) ? 'https' : 'http';

        $request = [
            'scheme'   => $scheme,
            'host'     => $this->serverValue('HTTP_HOST'),
            'port'     => $this->serverValue('SERVER_PORT'),
            'user'     => $this->serverValue('PHP_AUTH_USER'),
            'pass'     => $this->serverValue('PHP_AUTH_PW'),
            'path'     => '',
            'query'    => '',
            'fragment' => '',
        ];

        //Keep auto-convert to object. Less complexity for merge with array.
        $this->request = (object) array_merge($request, $parseUrl);
    }
}

<?php

namespace BFW;

/**
 * Class to get informations about http request.
 * Singleton pattern.
 */
class Request
{
    /**
     * @var \BFW\Request $instance Instance for this class (singleton pattern)
     */
    protected static $instance = null;

    /**
     * @var string $ip Client IP
     */
    protected $ip;

    /**
     * @var string $lang Client primary language
     */
    protected $lang;

    /**
     * @var string $referer Page referer
     */
    protected $referer;

    /**
     * @var string $method HTTP method (GET/POST/PUT/DELETE/...)
     */
    protected $method;

    /**
     * @var boolean $ssl If the request is with ssl (https) or not
     */
    protected $ssl;

    /**
     * @var \stdClass The request
     */
    protected $request;

    /**
     * Constructor
     * Call runDetect to detect all informations
     */
    protected function __construct()
    {
        $this->runDetect();
    }

    /**
     * Create singleton instance for this class
     * 
     * @return \BFW\Request
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Get the client IP
     * 
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Get the client primary language
     * 
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Get the referer url
     * 
     * @return string
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * Get the http method
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get information about if the request is ssl
     * 
     * @return boolean
     */
    public function getSsl()
    {
        return $this->ssl;
    }

    /**
     * Get the current request
     * 
     * @return \stdClass
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the information from the $_SERVER if exist.
     * If not exist, return a empty string.
     * 
     * @param string $keyName The key's value in $_SERVER array
     * 
     * @return string
     */
    public static function getServerVar($keyName)
    {
        if (!isset($_SERVER[$keyName])) {
            return '';
        }

        return $_SERVER[$keyName];
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
        $this->ip = self::getServerVar('REMOTE_ADDR');
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

        $acceptLang  = self::getServerVar('HTTP_ACCEPT_LANGUAGE');
        $acceptLangs = explode(',', $acceptLang);

        $firstLang = explode(';', $acceptLangs[0]);
        $lang      = strtolower($firstLang[0]);

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
        $this->referer = self::getServerVar('HTTP_REFERER');
    }

    /**
     * Detect the http method
     * 
     * @return void
     */
    protected function detectMethod()
    {
        $this->method = self::getServerVar('REQUEST_METHOD');
    }

    /**
     * Detect if the request is ssl (https)
     * 
     * @return void
     */
    protected function detectSsl()
    {
        $serverHttps = self::getServerVar('HTTPS');
        $fwdProto    = self::getServerVar('HTTP_X_FORWARDED_PROTO');
        $fwdSsl      = self::getServerVar('HTTP_X_FORWARDED_SSL');

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
     * Detect the request informations
     * 
     * @return void
     */
    protected function detectRequest()
    {
        $parseUrl = parse_url(self::getServerVar('REQUEST_URI'));

        $this->request = [
            'scheme'   => '',
            'host'     => self::getServerVar('HTTP_HOST'),
            'port'     => '',
            'user'     => '',
            'pass'     => '',
            'path'     => '',
            'query'    => '',
            'fragment' => '',
        ];

        $this->request = (object) array_merge($this->request, $parseUrl);
    }
}

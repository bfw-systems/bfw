<?php

namespace BFW\Memcache;

use \Exception;

/**
 * Class to manage connection to memcache(d) server with memcache lib
 */
class Memcache extends \Memcache
{
    //Include traits to add some methods
    use \BFW\Traits\Memcache;

    /**
     * @var array $config Config define on bfw config file for memcache(d)
     */
    protected $config;

    /**
     * Constructor.
     * Connect to servers
     * 
     * @throws Exception If PHP Version is >= 7.x (no memcache extension)
     */
    public function __construct()
    {
        //Check php version. No memcache lib for >= 7.x
        if (PHP_VERSION_ID > 70000) {
            throw new Exception(
                'PHP Memcache Extension not supported for PHP 7'
            );
        }
        
        $app          = \BFW\Application::getInstance();
        $this->config = $app->getConfig('memcached');

        $this->connectToServers();
    }

    /**
     * Connect to memcache(d) server defined on config
     * 
     * @return void
     */
    protected function connectToServers()
    {
        //Loop on declared server(s)
        foreach ($this->config['server'] as $server) {
            $this->getServerInfos($server);
            
            $host       = $server['host'];
            $port       = $server['port'];
            $timeout    = $server['timeout'];
            $persistent = $server['persistent'];
            
            //Not checked if port = (int) 0; Doc said to define to 0 for socket
            if (empty($host) || $port === null) {
                continue;
            }
            
            //Change method used to connect if it's a persistent connection
            $methodName = 'connect';
            if ($persistent === true) {
                $methodName = 'pconnect';
            }
            
            //If a timeout is declared
            //not found the default value for this parameters, so a if...
            if ($timeout !== null) {
                $this->{$methodName}($host, $port, $timeout);
                return;
            }
            
            $this->{$methodName}($host, $port);
        }
    }
}

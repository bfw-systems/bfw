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
     * @var \BFW\Application $app Instance of BFW Application
     */
    protected $app;

    /**
     * @var array $config Config define on bfw config file for memcache(d)
     */
    protected $config;

    /**
     * Constructor.
     * Connect to servers
     * 
     * @param \BFW\Application $app The BFW Application instance
     * 
     * @throws Exception If PHP Version is >= 7.x (no memcache extension)
     */
    public function __construct(\BFW\Application $app)
    {
        //Check php version. No memcache lib for >= 7.x
        if (PHP_VERSION_ID > 70000) {
            throw new Exception(
                'PHP Memcache Extension not supported for PHP 7'
            );
        }
        
        $this->app    = $app;
        $this->config = $this->app->getConfig('memcached');

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
            $host       = isset($server['host']) ? $server['host'] : null;
            $port       = isset($server['port']) ? $server['port'] : null;
            $timeout    = isset($server['timeout']) ? $server['timeout'] : null;
            $persistent = isset($server['persistent']) ? $server['persistent'] : false;
            
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

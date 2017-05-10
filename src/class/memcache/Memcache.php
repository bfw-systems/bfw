<?php

namespace BFW\Memcache;

use \Exception;

/**
 * Class to manage connection to memcache(d) server with memcache lib
 */
class Memcache extends \Memcache
{
    //Include Memcache trait to add some common methods with Memcached class
    use \BFW\Traits\Memcache;

    /**
     * @var array $config Config define into bfw config file for memcache(d)
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
                'PHP Memcache Extension is not supported on PHP >= 7.0.0'
            );
        }
        
        $app          = \BFW\Application::getInstance();
        $this->config = $app->getConfig()->getValue('memcached');

        $this->connectToServers();
    }

    /**
     * Connect to memcache(d) server(s) defined on config
     * 
     * @return void
     */
    protected function connectToServers()
    {
        //Loop on declared server(s)
        foreach ($this->config['servers'] as $server) {
            $this->getServerInfos($server);
            
            $host       = $server['host'];
            $port       = $server['port'];
            $weight     = $server['weight'];
            $timeout    = $server['timeout'];
            $persistent = $server['persistent'];
            
            //Not checked if port = (int) 0; Doc said to define to 0 for socket
            if (empty($host) || $port === null) {
                continue;
            }
            
            //Error "Memcache::addserver(): weight must be a positive integer"
            if ($weight === 0) {
                $weight = 1;
            }
            
            //If a timeout is declared
            //I not found the default value for this parameters, so an if...
            if ($timeout !== null) {
                $this->addServer($host, $port, $persistent, $weight, $timeout);
                continue;
            }
            
            $this->addServer($host, $port, $persistent, $weight);
        }
        
        //Check if connect is successfull
        $this->testConnect();
    }
}

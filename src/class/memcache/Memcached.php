<?php

namespace BFW\Memcache;

use \Exception;

/**
 * Class to manage connection to memcache(d) server with memcached lib
 */
class Memcached extends \Memcached
{
    //Include Memcache trait to add some common methods with Memcache class
    use \BFW\Traits\Memcache;

    /**
     * @var array $config Config define into bfw config file for memcache(d)
     */
    protected $config;

    /**
     * Constructor.
     * Call parent constructor with the persistentId if declared in config
     * Connect to servers.
     */
    public function __construct()
    {
        $app          = \BFW\Application::getInstance();
        $this->config = $app->getConfig('memcached');
        
        if (!empty($this->config['persistentId'])) {
            parent::__construct($this->config['persistentId']);
        } else {
            parent::__construct();
        }

        $this->connectToServers();
    }

    /**
     * Get the list of server already connected (persistent)
     * Loop on server declared into the config file.
     * Connect to server if not already connected
     * 
     * @return void
     */
    protected function connectToServers()
    {
        //Array for the list of server(s) to connect
        $addServers  = [];
        
        //Get all server already connected (persistent)
        $serversList = $this->generateServerList();
        
        //Loop on server declared into config
        foreach ($this->config['servers'] as $server) {
            $this->getServerInfos($server);
            
            $host   = $server['host'];
            $port   = $server['port'];
            $weight = $server['weight'];
            
            //not check if port = (int) 0; Doc said to define to 0 for socket.
            if (empty($host) || $port === null) {
                continue;
            }
            
            //search if the reading server is not already connected
            if (in_array($host.':'.$port, $serversList)) {
                continue;
            }
            
            //If not, add the server at the list to connect
            $addServers[] = [$host, $port, $weight];
        }

        //Connect to server(s)
        $this->addServers($addServers);
        
        //Check if connect is successfull
        $this->testConnect();
    }
    
    /**
     * Get the list of servers where we are already connected (persistent)
     * 
     * @return string[]
     */
    protected function generateServerList()
    {
        $serversList = $this->getServerList();
        $servers     = [];
        
        foreach ($serversList as $serverInfos) {
            $servers[] = $serverInfos['host'].':'.$serverInfos['port'];
        }
        
        return $servers;
    }
}

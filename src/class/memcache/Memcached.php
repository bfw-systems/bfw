<?php

namespace BFW\Memcache;

use \Exception;

/**
 * Class to manage connection to memcache(d) server with memcached lib
 */
class Memcached extends \Memcached
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
     * Call parent constructor with the persistentId if declared in config
     * Connect to servers.
     * 
     * @param \BFW\Application $app The BFW Application instance
     */
    public function __construct(\BFW\Application $app)
    {
        $this->app    = $app;
        $this->config = $this->app->getConfig('memcached');
        
        if (!empty($this->config['persistentId'])) {
            parent::__construct($this->config['persistentId']);
        } else {
            parent::__construct();
        }

        $this->connectToServers();
    }

    /**
     * Get the list of server already connected (persistent)
     * Loop on server declared in config.
     * Connect to server if not already connected
     * 
     * @return void
     */
    protected function connectToServers()
    {
        //Array to have the list of server to connect
        $addServers  = [];
        
        //Get all server already connected (persistent)
        $serversList = $this->generateServerList();
        
        //Loop on server declared and config and search server not connected
        foreach ($this->config['server'] as $server) {
            $host   = isset($server['host']) ? $server['host'] : null;
            $port   = isset($server['port']) ? $server['port'] : null;
            $weight = isset($server['weight']) ? $server['weight'] : 0;
            
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

        //Connect server in the list
        $this->addServers($addServers);
        
        //Check if connect is successfull
        $this->testConnect();
    }
    
    /**
     * addServer not created the connection. It's created at the first call
     * to the memcached servers.
     * 
     * So, we run the connect to all server declared
     * 
     * @return void
     */
    protected function testConnect()
    {
        $stats = $this->getStats();
        
        if (!is_array($stats)) {
            throw new Exception('No memcached server connected.');
        }
        
        foreach ($stats as $serverName => $serverStat) {
            if ($serverStat['uptime'] < 1) {
                throw new Exception(
                    'Memcached server '.$serverName.' not connected'
                );
            }
        }
    }
    
    /**
     * Get the list of servers we already connected
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

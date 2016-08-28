<?php

namespace BFW\Memcache;

use \Exception;

class Memcached extends \Memcached
{
    use \BFW\Traits\Memcache;
    
    protected $app;

    protected $config;

    public function __construct(\BFW\Application $app)
    {
        $this->app    = $app;
        $this->config = $this->app->getConfig('memcached');
        
        if(!empty($this->config['persistentId'])) {
            parent::__construct($this->config['persistentId']);
        } else {
            parent::__construct();
        }

        $this->connectToServers();
    }

    protected function connectToServers()
    {
        $addServers  = [];
        $serversList = $this->generateServerList();
        
        foreach ($this->config['server'] as $server) {
            $host   = isset($server['host']) ? $server['host'] : null;
            $port   = isset($server['port']) ? $server['port'] : null;
            $weight = isset($server['weight']) ? $server['weight'] : 0;
            
            //not check if port = (int) 0; Doc said to define to 0 for socket.
            if (empty($host) || $port === null) {
                continue;
            }
            
            //It should not be to readd the server
            //(for persistent mode particulary)
            if(in_array($host.':'.$port, $serversList)) {
                continue;
            }
            
            $addServers[] = [$host, $port, $weight];
        }

        $this->addServers($addServers);
        $this->testConnect();
    }
    
    /**
     * addServer not created the connection. It's created at the first call
     * to the memcached servers.
     */
    protected function testConnect()
    {
        $stats = $this->getStats();
        
        foreach ($stats as $serverName => $serverStat) {
            if ($serverStat['uptime'] < 1) {
                throw new Exception(
                    'Memcached server '.$serverName.' not connected'
                );
            }
        }
    }
    
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

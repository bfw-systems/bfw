<?php

namespace BFW\Memcache;

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
        }

        $this->connectToServers();
    }

    protected function connectToServers()
    {
        $addServers = [];
        foreach ($this->config['server'] as $server) {
            $host   = isset($server['host']) ? $server['host'] : null;
            $port   = isset($server['port']) ? $server['port'] : null;
            $weight = isset($server['weight']) ? $server['weight'] : 0;
            
            //not check if port = (int) 0; Doc said to define to 0 for socket.
            if (empty($host) || $port === null) {
                continue;
            }
            
            $addServers[] = [$host, $port, $weight];
        }

        $this->addServers($addServers);
    }
}

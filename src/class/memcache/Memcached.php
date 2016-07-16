<?php

namespace BFW\Memcache;

class Memcached extends \Memcached
{
    use \BFW\Traits\Memcache;
    
    protected $app;

    protected $config;

    public function __construct($app)
    {
        $this->app    = $app;
        $this->config = $this->app->getConfig('memcached');

        $this->connectToServers();
    }

    protected function connectToServers()
    {
        $addServers = [];
        foreach ($this->config['memcached']['server'] as $server) {
            $addServers[] = [$server['host'], $server['port']];
        }

        $this->addServers($addServers);
    }
}

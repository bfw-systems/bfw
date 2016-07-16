<?php

namespace BFW\Memcache;

class Memcache extends \Memcache
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
        foreach ($this->config['memcached']['server'] as $server) {
            $this->addServer($server['host'], $server['port']);
        }
    }
}

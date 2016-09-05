<?php

namespace BFW\Memcache;

class Memcache extends \Memcache
{
    use \BFW\Traits\Memcache;
    
    protected $app;

    protected $config;

    public function __construct(\BFW\Application $app)
    {
        if (PHP_VERSION_ID > 70000) {
            throw new Exception(
                'PHP Memcache Extension not supported for PHP 7'
            );
        }
        
        $this->app    = $app;
        $this->config = $this->app->getConfig('memcached');

        $this->connectToServers();
    }

    protected function connectToServers()
    {
        foreach ($this->config['server'] as $server) {
            $host       = isset($server['host']) ? $server['host'] : null;
            $port       = isset($server['port']) ? $server['port'] : null;
            $timeout    = isset($server['timeout']) ? $server['timeout'] : null;
            $persistent = isset($server['persistent']) ? $server['persistent'] : false;
            
            //not check if port = (int) 0; Doc said to define to 0 for socket.
            if (empty($host) || $port === null) {
                continue;
            }
            
            $methodName = 'connect';
            if ($persistent === true) {
                $methodName = 'pconnect';
            }
            
            if ($timeout !== null) {
                $this->{$methodName}($host, $port, $timeout);
                return;
            }
            
            $this->{$methodName}($host, $port);
        }
    }
}

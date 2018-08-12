<?php

namespace BFW\Core\AppSystems;

use \Exception;

class Memcached extends AbstractSystem
{
    /**
     * @var \BFW\Memcached|null $memcached The class used to connect to
     * memcache(d) server.
     * The class name should be declared into config file.
     */
    protected $memcached;
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Memcached|null
     */
    public function __invoke()
    {
        return $this->memcached;
    }
    
    /**
     * Getter accessor to property memcached
     * 
     * @return \BFW\Memcached|null
     */
    public function getMemcached()
    {
        return $this->memcached;
    }
    
    /**
     * {@inheritdoc}
     * Load and initialize le memcached object
     */
    public function init()
    {
        $this->loadMemcached();
        $this->initStatus = true;
    }
    
    /**
     * Connect to memcache(d) server with the class declared in config file
     * 
     * @return void
     * 
     * @throws \Exception If memcached is enabled but no class is define. Or if
     *  The class declared into the config is not found.
     */
    protected function loadMemcached()
    {
        $memcachedConfig = \BFW\Application::getInstance()
            ->getConfig()
            ->getValue('memcached', 'memcached.php')
        ;

        if ($memcachedConfig['enabled'] === false) {
            return;
        }

        $this->memcached = new \BFW\Memcached;
        $this->memcached->connectToServers();
    }
}

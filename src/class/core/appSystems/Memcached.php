<?php

namespace BFW\Core\AppSystems;

use \Exception;

class Memcached extends AbstractSystem
{
    /**
     * @const ERR_MEMCACHED_NOT_CLASS_DEFINED Exception code if memcache(d) is
     * enabled but the class to use is not defined.
     */
    const ERR_MEMCACHED_NOT_CLASS_DEFINED = 1507001;
    
    /**
     * @const ERR_MEMCACHED_CLASS_NOT_FOUND Exception code if the memcache(d)
     * class is not found.
     */
    const ERR_MEMCACHED_CLASS_NOT_FOUND = 1507002;
    
    /**
     * @const ERR_MEMCACHED_NOT_IMPLEMENT_INTERFACE Exception code the
     * memcache(d) class not implement the interface.
     */
    const ERR_MEMCACHED_NOT_IMPLEMENT_INTERFACE = 1507003;
    
    /**
     * @var \BFW\Memcache\MemcacheInterface|null $memcached The class used
     * to connect to memcache(d) server.
     * The class name should be declared into config file.
     */
    protected $memcached;
    
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return $this->memcached;
    }
    
    /**
     * Getter accessor to property memcached
     * 
     * @return \BFW\Memcache\MemcacheInterface|null
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
     * @return Object
     * 
     * @throws Exception If memcached is enabled but no class is define. Or if
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

        $class           = $this->obtainMemcachedClassName($memcachedConfig);
        $this->memcached = new $class;
        
        if (!($this->memcached instanceof \BFW\Memcache\MemcacheInterface)) {
            throw new Exception(
                'Memcache class '.$class.' not implement the interface.',
                $this::ERR_MEMCACHED_NOT_IMPLEMENT_INTERFACE
            );
        }
        
        $this->memcached->connectToServers();
    }
    
    /**
     * Obtain the memcache class name to use
     * 
     * @param array $memcachedConfig
     * 
     * @return string
     * 
     * @throws Exception If there are some problem with the config declared
     */
    protected function obtainMemcachedClassName($memcachedConfig)
    {
        $class = $memcachedConfig['class'];
        
        if (empty($class)) {
            throw new Exception(
                'Memcached is active but no class is define',
                $this::ERR_MEMCACHED_NOT_CLASS_DEFINED
            );
        }

        if (class_exists($class) === false) {
            throw new Exception(
                'Memcache class '.$class.' not found.',
                $this::ERR_MEMCACHED_CLASS_NOT_FOUND
            );
        }
        
        return $class;
    }
}

<?php

namespace BFW\Core\AppSystems;

class Config extends AbstractSystem
{
    /**
     * @var \BFW\Config $config The config object for BFW framework
     */
    protected $config;
    
    /**
     * Define config object and load all config file used by the framework
     */
    public function __construct()
    {
        $this->config = new \BFW\Config('bfw');
        $this->config->loadFiles();
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Config
     */
    public function __invoke()
    {
        return $this->config;
    }

    /**
     * Getter accessor to the property config
     * 
     * @return \BFW\Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}

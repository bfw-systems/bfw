<?php

namespace BFW\Core\AppSystems;

class Config extends AbstractSystem
{
    /**
     * @var \BFW\Config|null $config The config object for BFW framework
     */
    protected $config;
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Config|null
     */
    public function __invoke()
    {
        return $this->config;
    }

    /**
     * Getter accessor to the property config
     * 
     * @return \BFW\Config|null
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * {@inheritdoc}
     * Define config object and load all config file used by the framework
     */
    public function init()
    {
        $this->config = new \BFW\Config('bfw');
        $this->config->loadFiles();
        
        $this->initStatus = true;
    }
}

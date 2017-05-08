<?php

namespace BFW\test\unit\mocks;

/**
 * Mock for Module class
 */
class Module extends \BFW\Module
{
    /**
     * {@inheritdoc}
     * Reset properties config and loadInfos to empty object
     */
    public function __construct($pathName, $loadModule = true)
    {
        parent::__construct($pathName, $loadModule);
        
        $this->config    = new \stdClass;
        $this->loadInfos = new \stdClass;
    }
    
    /**
     * Set the config property to new value
     * 
     * @param mixed $config The new value
     * 
     * @return void
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }
    
    /**
     * Set the loadInfos property to new value
     * 
     * @param mixed $loadInfos The new value
     * 
     * @return void
     */
    public function setLoadInfos($loadInfos)
    {
        $this->loadInfos = $loadInfos;
    }
    
    /**
     * Set the status property to new value
     * 
     * @param mixed $load The new value for load property into status object
     * @param mixed $run The new value for run property into status object
     * 
     * @return void
     */
    public function setStatus($load, $run)
    {
        $this->status = (object) [
            'load' => $load,
            'run'  => $run
        ];
    }
}

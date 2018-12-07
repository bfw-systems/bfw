<?php

namespace BFW\Test\Mock;

/**
 * Mock for Module class
 */
class Module extends \BFW\Module
{
    /**
     * {@inheritdoc}
     * Reset properties config and loadInfos to empty object
     */
    public function __construct(string $pathName)
    {
        parent::__construct($pathName);
        
        $this->config    = new \stdClass;
        $this->loadInfos = new \stdClass;
    }
    
    /**
     * Called when a instance of this class is cloned.
     * Clone the instance for property which have an object too.
     */
    public function __clone()
    {
        $this->config    = clone $this->config;
        $this->loadInfos = clone $this->loadInfos;
        $this->status    = clone $this->status;
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
     * @param boolean $load The new value for load property into status object
     * @param boolean $run The new value for run property into status object
     * 
     * @return void
     */
    public function setStatus(bool $load, bool $run)
    {
        $this->status->load = $load;
        $this->status->run  = $run;
    }
    
    /**
     * Call the static method readJsonFile
     * 
     * @param array $args args to pass at readJsonFile
     * 
     * @return mixed
     */
    public function callReadJsonFile(...$args)
    {
        return self::readJsonFile(...$args);
    }
}

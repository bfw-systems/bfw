<?php

namespace BFW\Core\AppSystems;

/**
 * Abstract class for Core System.
 * Implement SystemInterface and define some methods with the default behavior
 */
abstract class AbstractSystem implements SystemInterface
{
    /**
     * @var boolean $initStatus To know if the init method has been called
     */
    protected $initStatus = false;
    
    /**
     * @var boolean $runStatus To know if the run method has been called
     */
    protected $runStatus = false;
    
    /**
     * PHP Magic method
     * Called when the class is called like a function
     * 
     * @return mixed
     */
    abstract function __invoke();
    
    /**
     * {@inheritdoc}
     * Should change initStatus to true.
     */
    public function init()
    {
        $this->initStatus = true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isInit(): bool
    {
        return $this->initStatus;
    }
    
    /**
     * {@inheritdoc}
     */
    public function toRun(): bool
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isRun(): bool
    {
        return $this->runStatus;
    }
    
    /**
     * {@inheritdoc}
     * Should change runStatus to true.
     */
    public function run()
    {
        $this->runStatus = true;
    }
}

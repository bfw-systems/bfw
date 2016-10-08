<?php

namespace BFW;

/**
 * Linker between controller and router module.
 */
class ControllerRouterLink
{
    /**
     * @var \BFW\ControllerRouterLink $instance Current instance (Singleton)
     */
    protected static $instance;

    /**
     * @var mixed $target : The target to call by controller
     */
    protected $target;
    
    /**
     * @var mixed $datas : Some datas send by router module to controller
     */
    protected $datas;
    
    /**
     * Constructor
     * Singleton pattern
     */
    protected function __construct()
    {
        //Nothing todo
    }
    
    /**
     * Contructor for singleton pattern
     * Get the instance of this class
     * 
     * @return \BFW\ControllerRouterLink
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            $calledClass = get_called_class(); //Autorize extends this class
            
            self::$instance = new $calledClass();
        }

        return self::$instance;
    }
    
    /**
     * Getter for property target
     * 
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }
    
    /**
     * Setter for property target
     * 
     * @param mixed $target
     * 
     * @return \BFW\ControllerRouterLink
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }
    
    /**
     * Getter for property datas
     * 
     * @return mixed
     */
    public function getDatas()
    {
        return $this->datas;
    }
    
    /**
     * Setter for property datas
     * 
     * @param mixed $datas
     * 
     * @return \BFW\ControllerRouterLink
     */
    public function setDatas($datas)
    {
        $this->datas = $datas;
        return $this;
    }
}

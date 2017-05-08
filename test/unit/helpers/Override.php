<?php

namespace BFW\test\helpers;

/**
 * Trait to override one or many class's methods
 */
trait Override
{
    /**
     * @var array $overrideMethods List of overrided methods
     *  The key should be the method name
     *  The value should be a callable or a data
     */
    public $overrideMethods = [];
    
    /**
     * Add (or remplace) a new overrided method
     * 
     * @param string $method The method name
     * @param callable|mixed $return The callable or data to return
     */
    public function addOverridedMethod($method, $return)
    {
        $this->overrideMethods = array_merge(
            [$method => $return],
            $this->overrideMethods
        );
    }
    
    /**
     * Check if a method is overrided or not
     * 
     * @param string $method The method name
     * 
     * @return boolean
     */
    protected function isOverrided($method)
    {
        return array_key_exists($method, $this->overrideMethods);
    }
    
    /**
     * Call the overrided method if the method is overrided or call the
     * original method
     * 
     * @param string $method The method name
     * @param mixed[] $args All arguments passed to the method
     * 
     * @return mixed
     */
    protected function callOverrideOrParent($method, $args)
    {
        if ($this->isOverrided($method)) {
            return $this->callOverride($method, $args);
        }
        
        return parent::{$method}(...$args);
    }
    
    /**
     * Call a overrided method
     * If the override is a callable, it will be call and it's return is
     * returned by this method.
     * Else, this method return directly the data.
     * 
     * @param string $method The method name
     * @param mixed[] $args All arguments passed to the method
     * 
     * @return mixed
     */
    protected function callOverride($method, $args)
    {
        $overrided = $this->overrideMethods[$method];
        if (is_callable($overrided)) {
            //$overrided->call($this, ...$args) : >= PHP7
            $overrided = $overrided->bindTo($this, $this);
            $overrided(...$args);
        }
        
        return $overrided;
    }
}

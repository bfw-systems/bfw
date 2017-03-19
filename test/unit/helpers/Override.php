<?php

namespace BFW\test\helpers;

trait Override
{
    public $overrideMethods = [];
    
    public function addOverridedMethod($method, $return)
    {
        $this->overrideMethods = array_merge(
            [$method => $return],
            $this->overrideMethods
        );
    }
    
    protected function isOverrided($method)
    {
        return array_key_exists($method, $this->overrideMethods);
    }
    
    protected function callOverrideOrParent($method, $args)
    {
        if ($this->isOverrided($method)) {
            return $this->callOverride($method, $args);
        }
        
        return parent::{$method}(...$args);
    }
    
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

<?php

namespace BFW\test\unit\mocks;

class Application extends ApplicationForceConfig
{
    public static function removeInstance()
    {
        self::$instance = null;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function getModules()
    {
        return $this->modules;
    }
    
    public function getRunPhases()
    {
        return $this->runPhases;
    }
}

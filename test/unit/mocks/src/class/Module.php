<?php

namespace BFW\test\unit\mocks;

class Module extends \BFW\Module
{
    public function __construct($pathName, $loadModule = true)
    {
        parent::__construct($pathName, $loadModule);
        
        $this->config       = new \stdClass;
        $this->loadInfos    = new \stdClass;
    }
    
    public function forceConfig($config)
    {
        $this->config = $config;
    }
    
    public function forceLoadInfos($loadInfos)
    {
        $this->loadInfos = $loadInfos;
    }
    
    public function forceStatus($load, $run)
    {
        $this->status = (object) [
            'load' => $load,
            'run'  => $run
        ];
    }
}

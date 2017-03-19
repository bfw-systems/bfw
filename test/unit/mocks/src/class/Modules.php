<?php

namespace BFW\test\unit\mocks;

class Modules extends \BFW\Modules
{
    protected static $config = [];
    protected static $loadInfos = [];
    
    public static function declareModuleConfig($moduleName, $config) {
        self::$config[$moduleName] = $config;
    }
    
    public static function declareModuleLoadInfos($moduleName, $loadInfos) {
        self::$loadInfos[$moduleName] = $loadInfos;
    }
    
    public function addModule($moduleName)
    {
        $this->modules[$moduleName] = new \BFW\test\unit\mocks\Module($moduleName, false);
        
        if (isset(self::$config[$moduleName])) {
            $this->modules[$moduleName]->forceConfig(self::$config[$moduleName]);
        }
        
        if (isset(self::$loadInfos[$moduleName])) {
            $this->modules[$moduleName]->forceLoadInfos(self::$loadInfos[$moduleName]);
        }
        
        $this->modules[$moduleName]->forceStatus(true, false);
    }
}

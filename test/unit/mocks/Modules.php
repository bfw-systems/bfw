<?php

namespace BFW\test\unit\mocks;

class Modules extends \BFW\Modules
{
    protected static $config = [];
    protected static $loadInfos = [];
    protected static $installInfos = [];
    
    public static function declareModuleConfig($moduleName, $config) {
        self::$config[$moduleName] = $config;
    }
    
    public static function declareModuleLoadInfos($moduleName, $loadInfos) {
        self::$loadInfos[$moduleName] = $loadInfos;
    }
    
    public static function declareModuleInstallInfos($moduleName, $installInfos) {
        self::$installInfos[$moduleName] = $installInfos;
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
        
        if (isset(self::$installInfos[$moduleName])) {
            $this->modules[$moduleName]->forceInstallInfos(self::$installInfos[$moduleName]);
        }
        
        $this->modules[$moduleName]->forceStatus(true, false);
    }
}

<?php

namespace BFW\test\unit\mocks;

/**
 * Mock for Modules class
 */
class Modules extends \BFW\Modules
{
    /**
     * @var array $config Config for each module to add (can be different)
     */
    protected static $config = [];
    
    /**
     * @var array $loadInfos Load informations for each module to add
     * (can be different)
     */
    protected static $loadInfos = [];
    
    /**
     * Setter for static property $config
     * 
     * @param string $moduleName The module name
     * @param mixed $config The config to use for this module
     * 
     * @return void
     */
    public static function setModuleConfig($moduleName, $config) {
        self::$config[$moduleName] = $config;
    }
    
    /**
     * Setter for static property $config
     * 
     * @param string $moduleName The module name
     * @param mixed $loadInfos The load informations to use for this module
     * 
     * @return void
     */
    public static function setModuleLoadInfos($moduleName, $loadInfos) {
        self::$loadInfos[$moduleName] = $loadInfos;
    }
    
    /**
     * {@inheritdoc}
     */
    public function addModule($moduleName)
    {
        $this->modules[$moduleName] = new \BFW\test\unit\mocks\Module($moduleName, false);
        
        if (isset(self::$config[$moduleName])) {
            $this->modules[$moduleName]->setConfig(self::$config[$moduleName]);
        }
        
        if (isset(self::$loadInfos[$moduleName])) {
            $this->modules[$moduleName]->setLoadInfos(self::$loadInfos[$moduleName]);
        }
        
        $this->modules[$moduleName]->setStatus(true, false);
    }
}

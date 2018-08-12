<?php

namespace BFW\Test\Mock;

/**
 * Mock for ModuleList class
 */
class ModuleList extends \BFW\ModuleList
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
    public static function setModuleConfig(string $moduleName, $config)
    {
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
    public static function setModuleLoadInfos(string $moduleName, $loadInfos)
    {
        self::$loadInfos[$moduleName] = $loadInfos;
    }
    
    /**
     * {@inheritdoc}
     */
    public function addModule(string $moduleName)
    {
        $this->modules[$moduleName] = new \BFW\Test\Mock\Module($moduleName, false);
        
        if (isset(self::$config[$moduleName])) {
            $this->modules[$moduleName]->setConfig(self::$config[$moduleName]);
        }
        
        if (isset(self::$loadInfos[$moduleName])) {
            $this->modules[$moduleName]->setLoadInfos(self::$loadInfos[$moduleName]);
        }
        
        $this->modules[$moduleName]->setStatus(true, false);
    }
}

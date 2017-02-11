<?php

namespace BFW\Install\test\unit\mocks;

class Application extends ApplicationForceConfig
{
    public $modulesToAdd = [];
    
    protected function runCliFile()
    {
        //Do nothing.
        //Will be test with a installer test
    }

    protected function initModules()
    {
        parent::initModules();
        $this->modules = new \BFW\test\unit\mocks\Modules;
    }
    
    protected function readAllModules()
    {
        $modules = $this->modules;
        foreach($this->modulesToAdd as $moduleName => $module) {
            $modules::declareModuleConfig($moduleName, $module->config);
            $modules::declareModuleLoadInfos($moduleName, $module->loadInfos);
            $modules::declareModuleInstallInfos($moduleName, $module->installInfos);
        }
        
        //parent::readAllModules();
    }
    
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

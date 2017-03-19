<?php

namespace BFW\test\helpers;

//To have the config mock loaded for external module which use this class.
require_once(__DIR__.'/../mocks/src/class/ConfigForceDatas.php');

trait Application
{
    use \BFW\test\helpers\Override;
    
    public $modulesToAdd = [];
    
    protected function initSystem($options)
    {
        if (array_key_exists('overrideMethods', $options)) {
            $this->overrideMethods = $options['overrideMethods'];
        } elseif (array_key_exists('overrideAllMethods', $options)) {
            $this->overrideMethods = [
                'initOptions'        => true,
                'initConstants'      => true,
                'initComposerLoader' => true,
                'initConfig'         => true,
                'initRequest'        => true,
                'initSession'        => true,
                'initErrors'         => true,
                'initModules'        => true,
                'declareRunPhases'   => true
            ];
        }
        
        if (array_key_exists('forceConfig', $options)) {
            $this->addOverridedMethod(
                'initConfig',
                function() use (&$options) {
                    $this->config = new \BFW\test\unit\mocks\ConfigForceDatas('bfw');
                    $this->config->forceConfig('bfw', $options['forceConfig']);
                }
            );
        }
        
        return parent::initSystem($options);
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
    
    public function forceConfig($config)
    {
        $this->config->forceConfig('bfw', $config);
    }
    
    public function updateKey($configKey, $newValue)
    {
        $this->config->updateKey('bfw', $configKey, $newValue);
    }
    
    //**** Override all methods ****\\
    
    public function getComposerLoader()
    {
        return $this->callOverrideOrParent('getComposerLoader', []);
    }
    
    public function getConfig($configKey)
    {
        return $this->callOverrideOrParent('getConfig', [$configKey]);
    }
    
    public function getMemcached()
    {
        return $this->callOverrideOrParent('getMemcached', []);
    }
    
    public function getModule($moduleName)
    {
        return $this->callOverrideOrParent('getModule', [$moduleName]);
    }
    
    public function getOption($optionKey)
    {
        return $this->callOverrideOrParent('getOption', [$optionKey]);
    }
    
    public function getRequest()
    {
        return $this->callOverrideOrParent('getRequest', []);
    }
    
    protected function initOptions($options)
    {
        return $this->callOverrideOrParent('initOptions', [$options]);
    }
    
    protected function initConstants()
    {
        return $this->callOverrideOrParent('initConstants', []);
    }
    
    protected function initComposerLoader()
    {
        return $this->callOverrideOrParent('initComposerLoader', []);
    }
    
    protected function initConfig()
    {
        return $this->callOverrideOrParent('initConfig', []);
    }
    
    protected function initRequest()
    {
        return $this->callOverrideOrParent('initRequest', []);
    }
    
    protected function initSession()
    {
        return $this->callOverrideOrParent('initSession', []);
    }
    
    protected function initErrors()
    {
        return $this->callOverrideOrParent('initErrors', []);
    }
    
    protected function initModules()
    {
        return $this->callOverrideOrParent('initModules', []);
    }
    
    protected function addComposerNamespaces()
    {
        return $this->callOverrideOrParent('addComposerNamespaces', []);
    }
    
    protected function declareRunPhases()
    {
        return $this->callOverrideOrParent('declareRunPhases', []);
    }
    
    public function run()
    {
        return $this->callOverrideOrParent('run', []);
    }
    
    protected function loadMemcached()
    {
        return $this->callOverrideOrParent('loadMemcached', []);
    }
    
    protected function readAllModules()
    {
        return $this->callOverrideOrParent('readAllModules', []);
    }
    
    protected function loadAllCoreModules()
    {
        return $this->callOverrideOrParent('loadAllCoreModules', []);
    }
    
    protected function loadAllAppModules()
    {
        return $this->callOverrideOrParent('loadAllAppModules', []);
    }
    
    protected function loadModule($moduleName)
    {
        return $this->callOverrideOrParent('loadModule', [$moduleName]);
    }
    
    protected function runCliFile()
    {
        return $this->callOverrideOrParent('runCliFile', []);
    }
}

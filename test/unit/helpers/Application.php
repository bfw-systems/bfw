<?php

namespace BFW\test\helpers;

//To have the config mock loaded for external module which use this class.
require_once(__DIR__.'/../mocks/src/class/ConfigForceDatas.php');
require_once(__DIR__.'/Override.php');

/**
 * Trait used for mock Application class
 */
trait Application
{
    use \BFW\test\helpers\Override;
    
    /**
     * @var array $modulesToAdd : Module's list to add
     */
    public $modulesToAdd = [];
    
    /**
     * @see \Application::initSystem
     */
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
    
    /**
     * Remove the current instance used by Singleton pattern
     * 
     * @return void
     */
    public static function removeInstance()
    {
        self::$instance = null;
    }
    
    /**
     * Getter to $errors property
     * 
     * @return \BFW\Core\Errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Getter to $errors property
     * 
     * @return \BFW\Modules
     */
    public function getModules()
    {
        return $this->modules;
    }
    
    /**
     * Getter to $errors property
     * 
     * @return array[]
     */
    public function getRunSteps()
    {
        return $this->runSteps;
    }
    
    /**
     * Call the method forceConfig of the mocked Config object
     * Change all the config of BFW
     * 
     * @return void
     */
    public function forceConfig($config)
    {
        $this->config->forceConfig('bfw', $config);
    }
    
    /**
     * Call the method updateKey of the mocked Config object
     * Change a key of the BFW config
     * 
     * @return void
     */
    public function updateKey($configKey, $newValue)
    {
        $this->config->updateKey('bfw', $configKey, $newValue);
    }
    
    //**** Override all methods ****\\
    
    /**
     * @see \Application::getComposerLoader
     * Present because the method can be overrided during the test
     */
    public function getComposerLoader()
    {
        return $this->callOverrideOrParent('getComposerLoader', []);
    }
    
    /**
     * @see \Application::getConfig
     * Present because the method can be overrided during the test
     */
    public function getConfig($configKey)
    {
        return $this->callOverrideOrParent('getConfig', [$configKey]);
    }
    
    /**
     * @see \Application::getMemcached
     * Present because the method can be overrided during the test
     */
    public function getMemcached()
    {
        return $this->callOverrideOrParent('getMemcached', []);
    }
    
    /**
     * @see \Application::getModule
     * Present because the method can be overrided during the test
     */
    public function getModule($moduleName)
    {
        return $this->callOverrideOrParent('getModule', [$moduleName]);
    }
    
    /**
     * @see \Application::getOption
     * Present because the method can be overrided during the test
     */
    public function getOption($optionKey)
    {
        return $this->callOverrideOrParent('getOption', [$optionKey]);
    }
    
    /**
     * @see \Application::getRequest
     * Present because the method can be overrided during the test
     */
    public function getRequest()
    {
        return $this->callOverrideOrParent('getRequest', []);
    }
    
    /**
     * @see \Application::initOptions
     * Present because the method can be overrided during the test
     */
    protected function initOptions($options)
    {
        return $this->callOverrideOrParent('initOptions', [$options]);
    }
    
    /**
     * @see \Application::initConstants
     * Present because the method can be overrided during the test
     */
    protected function initConstants()
    {
        return $this->callOverrideOrParent('initConstants', []);
    }
    
    /**
     * @see \Application::initComposerLoader
     * Present because the method can be overrided during the test
     */
    protected function initComposerLoader()
    {
        return $this->callOverrideOrParent('initComposerLoader', []);
    }
    
    /**
     * @see \Application::initConfig
     * Present because the method can be overrided during the test
     */
    protected function initConfig()
    {
        return $this->callOverrideOrParent('initConfig', []);
    }
    
    /**
     * @see \Application::initRequest
     * Present because the method can be overrided during the test
     */
    protected function initRequest()
    {
        return $this->callOverrideOrParent('initRequest', []);
    }
    
    /**
     * @see \Application::initSession
     * Present because the method can be overrided during the test
     */
    protected function initSession()
    {
        return $this->callOverrideOrParent('initSession', []);
    }
    
    /**
     * @see \Application::initErrors
     * Present because the method can be overrided during the test
     */
    protected function initErrors()
    {
        return $this->callOverrideOrParent('initErrors', []);
    }
    
    /**
     * @see \Application::initModules
     * Present because the method can be overrided during the test
     */
    protected function initModules()
    {
        return $this->callOverrideOrParent('initModules', []);
    }
    
    /**
     * @see \Application::addComposerNamespaces
     * Present because the method can be overrided during the test
     */
    protected function addComposerNamespaces()
    {
        return $this->callOverrideOrParent('addComposerNamespaces', []);
    }
    
    /**
     * @see \Application::declareRunSteps
     * Present because the method can be overrided during the test
     */
    protected function declareRunSteps()
    {
        return $this->callOverrideOrParent('declareRunSteps', []);
    }
    
    /**
     * @see \Application::run
     * Present because the method can be overrided during the test
     */
    public function run()
    {
        return $this->callOverrideOrParent('run', []);
    }
    
    /**
     * @see \Application::loadMemcached
     * Present because the method can be overrided during the test
     */
    protected function loadMemcached()
    {
        return $this->callOverrideOrParent('loadMemcached', []);
    }
    
    /**
     * @see \Application::readAllModules
     * Present because the method can be overrided during the test
     */
    protected function readAllModules()
    {
        return $this->callOverrideOrParent('readAllModules', []);
    }
    
    /**
     * @see \Application::loadAllCoreModules
     * Present because the method can be overrided during the test
     */
    protected function loadAllCoreModules()
    {
        return $this->callOverrideOrParent('loadAllCoreModules', []);
    }
    
    /**
     * @see \Application::loadAllAppModules
     * Present because the method can be overrided during the test
     */
    protected function loadAllAppModules()
    {
        return $this->callOverrideOrParent('loadAllAppModules', []);
    }
    
    /**
     * @see \Application::loadModule
     * Present because the method can be overrided during the test
     */
    protected function loadModule($moduleName)
    {
        return $this->callOverrideOrParent('loadModule', [$moduleName]);
    }
    
    /**
     * @see \Application::runCliFile
     * Present because the method can be overrided during the test
     */
    protected function runCliFile()
    {
        return $this->callOverrideOrParent('runCliFile', []);
    }
}

<?php

namespace BFW\Install\Test\Mock;

//To be included by module who use it
require_once(__DIR__.'/../Modules.php');

class Application extends \BFW\Install\Application
{
    /**
     * @var array|object $mockedConfigValues The values to use for config
     */
    protected $mockedConfigValues;
    
    /**
     * @var \stdClass[] $mockedModulesList List of fake module to load, the
     * value is an object with properties "config" and "loadInfos" used to
     * declare the fake module.
     */
    protected $mockedModulesList = [];
    
    /**
     * Setter to parent property runSteps
     * 
     * @param callable[] $runSteps The new runSteps value
     * 
     * @return $this
     */
    public function setRunSteps($runSteps)
    {
        $this->runSteps = $runSteps;
        return $this;
    }
    
    /**
     * Getter to property mockedConfigValues
     * 
     * @return array|object
     */
    public function getMockedConfigValues()
    {
        return $this->mockedConfigValues;
    }
    
    /**
     * Setter to property mockedConfigValues
     * 
     * @param array|object $mockedConfigValues
     * 
     * @return $this
     */
    public function setMockedConfigValues($mockedConfigValues)
    {
        $this->mockedConfigValues = $mockedConfigValues;
        return $this;
    }
    
    /**
     * Getter to property mockedModulesList
     * 
     * @return \stdClass[]
     */
    public function getMockedModulesList()
    {
        return $this->mockedModulesList;
    }
    
    /**
     * Add a new fake module to the list
     * 
     * @param string $moduleName The name of the module
     * @param \stdClass $mockedModulesInfos An object with properties "config"
     * and "loadInfos" used to declare the fake module.
     * 
     * @return $this
     */
    public function addMockedModulesList(
        $moduleName,
        \stdClass $mockedModulesInfos
    ) {
        $this->mockedModulesList[$moduleName] = $mockedModulesInfos;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * Define the Config object and use the value of the property
     * mockedConfigValues to set a fake config.
     * If mockedConfigValues is null, read the default config file and set
     * the property value with it.
     */
    protected function initConfig()
    {
        if ($this->mockedConfigValues === null) {
            $this->mockedConfigValues = require(
                $this->options->getValue('vendorDir')
                .'/bulton-fr/bfw/skel/app/config/bfw/config.php'
            );
        }
        
        $this->config = new \BFW\Config('bfw');
        $this->config->setConfigForFile('config.php', $this->mockedConfigValues);
    }
    
    /**
     * {@inheritdoc}
     * Use the mocked class
     */
    protected function initModules()
    {
        $this->modules = new \BFW\Test\Mock\Modules;
    }
    
    /**
     * {@inheritdoc}
     * Use the property mockedModulesList to declare all fake modules before
     * call the parent method.
     */
    protected function loadAllModules()
    {
        $modules = $this->modules;
        foreach($this->mockedModulesList as $moduleName => $module) {
            $modules::setModuleConfig($moduleName, $module->config);
            $modules::setModuleLoadInfos($moduleName, $module->loadInfos);
        }
        
        parent::loadAllModules();
    }
}

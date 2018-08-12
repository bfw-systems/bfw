<?php

namespace BFW\Core\AppSystems\Test\Mock;

//To be included by module who use it
require_once(__DIR__.'/../../ModuleList.php');

class ModuleList extends \BFW\Core\AppSystems\ModuleList
{
    /**
     * @var \stdClass[] $mockedList List of fake module to load, the
     * value is an object with properties "config" and "loadInfos" used to
     * declare the fake module.
     */
    protected $mockedList = [];
    
    public function getMockedList(): array
    {
        return $this->mockedList;
    }
    
    public function init()
    {
        $this->moduleList = new \BFW\Test\Mock\ModuleList;
        $this->initStatus = true;
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
    public function addToMockedList(
        string $moduleName,
        \stdClass $mockedModulesInfos
    ): self {
        $this->mockedList[$moduleName] = $mockedModulesInfos;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * Use the property mockedModulesList to declare all fake modules before
     * call the parent method.
     */
    protected function loadAllModules()
    {
        $moduleList = $this->moduleList;
        foreach($this->mockedList as $moduleName => $module) {
            $moduleList::setModuleConfig($moduleName, $module->config);
            $moduleList::setModuleLoadInfos($moduleName, $module->loadInfos);
        }
        
        parent::loadAllModules();
    }
}

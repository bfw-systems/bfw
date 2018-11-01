<?php

namespace BFW\Test\Mock\Install\Core\AppSystems;

//To be included by module who use it
require_once(__DIR__.'/../../../Core/ModuleList.php');

class ModuleList extends \BFW\Install\Core\AppSystems\ModuleList
{
    /**
     * @var \stdClass[] $mockedList List of fake module to load, the
     * value is an object with properties "config" and "loadInfos" used to
     * declare the fake module.
     */
    protected static $mockedList = [];
    
    public static function getMockedList(): array
    {
        return self::$mockedList;
    }
    
    public function __construct()
    {
        $this->moduleList = new \BFW\Test\Mock\Core\ModuleList;
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
    public static function addToMockedList(
        string $moduleName,
        \stdClass $mockedModulesInfos
    ) {
        self::$mockedList[$moduleName] = $mockedModulesInfos;
    }
    
    /**
     * {@inheritdoc}
     * Use the property mockedModulesList to declare all fake modules before
     * call the parent method.
     */
    protected function loadAllModules()
    {
        $moduleList = $this->moduleList;
        foreach(self::$mockedList as $moduleName => $module) {
            $moduleList::setModuleConfig($moduleName, $module->config);
            $moduleList::setModuleLoadInfos($moduleName, $module->loadInfos);
        }
        
        parent::loadAllModules();
    }
}

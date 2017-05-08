<?php

namespace BFW;

use \Exception;

/**
 * Class to manage all modules in the application
 */
class Modules
{
    /**
     * @var \BFW\Module[] All module instance
     */
    protected $modules = [];

    /**
     * @var array $loadTree The dependency tree for all modules
     */
    protected $loadTree = [];

    /**
     * Get the module's list
     * 
     * @return \BFW\Module[]
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Get the dependency tree
     * 
     * @return array
     */
    public function getLoadTree()
    {
        return $this->loadTree;
    }

    /**
     * Add a module to the modules's list
     * And instantiate \BFW\Module for this module
     * 
     * @param string $moduleName The module's name
     * 
     * @return void
     */
    public function addModule($moduleName)
    {
        $this->modules[$moduleName] = new \BFW\Module($moduleName);
    }

    /**
     * Get the \BFW\Module instance for a module
     * 
     * @param string $moduleName The module's name
     * 
     * @return \BFW\Module
     * 
     * @throws Exception If the module is not found
     */
    public function getModule($moduleName)
    {
        if (!isset($this->modules[$moduleName])) {
            throw new Exception('The Module '.$moduleName.' has not been found.');
        }

        return $this->modules[$moduleName];
    }
    
    /**
     * Read the "needMe" property for each module and add the dependency
     * 
     * @throws \Exception If the dependency is not found
     * 
     * @return void
     */
    public function readNeedMeDependencies()
    {
        foreach ($this->modules as $readModuleName => $module) {
            $loadInfos = $module->getLoadInfos();
            
            if (!property_exists($loadInfos, 'needMe')) {
                continue;
            }
            
            $needMe = (array) $loadInfos->needMe;
            foreach ($needMe as $needModuleName) {
                if (!isset($this->modules[$needModuleName])) {
                    throw new Exception(
                        'Module error: '.$readModuleName
                        .' need '.$needModuleName
                        .' but the module has not been found.'
                    );
                }
                
                $this->modules[$needModuleName]->addDependency(
                    $readModuleName
                );
            }
        }
    }

    /**
     * Generate the dependency tree for all declared module
     * 
     * @return void
     */
    public function generateTree()
    {
        $tree = new \bultonFr\DependencyTree\DependencyTree;

        foreach ($this->modules as $moduleName => $module) {
            $priority = 0;
            $depends  = [];

            $loadInfos = $module->getLoadInfos();
            if (property_exists($loadInfos, 'priority')) {
                $priority = (int) $loadInfos->priority;
            }
            if (property_exists($loadInfos, 'require')) {
                $depends = (array) $loadInfos->require;
            }

            $tree->addDependency($moduleName, $priority, $depends);
        }

        $this->loadTree = $tree->generateTree();
    }
}

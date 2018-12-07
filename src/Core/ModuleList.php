<?php

namespace BFW\Core;

use \Exception;

/**
 * Class to manage all modules in the application
 */
class ModuleList
{
    /**
     * @const ERR_NOT_FOUND Exception code if a module is not found
     */
    const ERR_NOT_FOUND = 1204001;
    
    /**
     * @const ERR_NEEDED_NOT_FOUND Exception code if a needed dependency is
     * not found.
     */
    const ERR_NEEDED_NOT_FOUND = 1204002;
    
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
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * Get the dependency tree
     * 
     * @return array
     */
    public function getLoadTree(): array
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
    public function addModule(string $moduleName)
    {
        $this->modules[$moduleName] = new \BFW\Module($moduleName);
        $this->modules[$moduleName]->loadModule();
    }

    /**
     * Get the \BFW\Module instance for a module
     * 
     * @param string $moduleName The module's name
     * 
     * @return \BFW\Module
     * 
     * @throws \Exception If the module is not found
     */
    public function getModuleByName(string $moduleName): \BFW\Module
    {
        if (!isset($this->modules[$moduleName])) {
            throw new Exception(
                'The Module '.$moduleName.' has not been found.',
                $this::ERR_NOT_FOUND
            );
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
                        .' but the module has not been found.',
                        $this::ERR_NEEDED_NOT_FOUND
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

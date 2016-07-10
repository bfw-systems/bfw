<?php

namespace BFW;

use \Exception;

class Modules
{
    protected $modules  = [];
    protected $loadTree = [];
    
    public function getModules()
    {
        return $this->modules;
    }
    
    public function getLoadTree()
    {
        return $this->loadTree;
    }
    
    public function addModule($moduleName)
    {
        $this->modules[$moduleName] = new \BFW\Module($moduleName);
    }
    
    public function getModule($moduleName)
    {
        if(!isset($this->modules[$moduleName])) {
            throw new Exception('Module '.$moduleName.' not find.');
        }
        
        return $this->modules[$moduleName];
    }
    
    public function generateTree()
    {
        $tree = new \bultonFr\DependencyTree\DependencyTree;
        
        foreach($this->modules as $moduleName => $module) {
            $priority = 0;
            $depends  = [];
            
            $loadInfos = $module->getLoadInfos();
            if(property_exists($loadInfos, 'priority')) {
                $priority = (int) $loadInfos->priority;
            }
            if(property_exists($loadInfos, 'require')) {
                $depends = (array) $loadInfos->require;
            }
            
            $tree->addDependency($moduleName, $priority, $depends);
        }
        
        $this->loadTree = $tree->generateTree();
    }
}

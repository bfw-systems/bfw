<?php

namespace BFW\Test\Mock;

//To be included by module who use it
require_once(__DIR__.'/core/appSystems/Cli.php');
require_once(__DIR__.'/core/appSystems/Config.php');
require_once(__DIR__.'/core/appSystems/Errors.php');
require_once(__DIR__.'/core/appSystems/ModuleList.php');

class Application extends \BFW\Application
{
    protected function defineCoreSystemList()
    {
        parent::defineCoreSystemList();
        
        $this->coreSystemList['cli']        = new \BFW\Core\AppSystems\Test\Mock\Cli;
        $this->coreSystemList['config']     = new \BFW\Core\AppSystems\Test\Mock\Config;
        $this->coreSystemList['errors']     = new \BFW\Core\AppSystems\Test\Mock\Errors;
        $this->coreSystemList['moduleList'] = new \BFW\Core\AppSystems\Test\Mock\ModuleList;
    }
    
    public function setCoreSystemList($coreSystemList)
    {
        $this->coreSystemList = $coreSystemList;
        return $this;
    }
    
    public function addToCoreSystemList($name, $system)
    {
        $this->coreSystemList[$name] = $system;
        return $this;
    }

    public function setDeclaredOptions($declaredOptions)
    {
        $this->declaredOptions = $declaredOptions;
        return $this;
    }

    public function setRunTasks(\BFW\RunTasks $runTasks)
    {
        $this->runTasks = $runTasks;
        return $this;
    }
}

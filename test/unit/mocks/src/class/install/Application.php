<?php

namespace BFW\Install\Test\Mock;

//To be included by module who use it
require_once(__DIR__.'/../ModuleList.php');
require_once(__DIR__.'/../core/appSystems/Config.php');

class Application extends \BFW\Install\Application
{
    protected function defineCoreSystemList()
    {
        parent::defineCoreSystemList();
        
        $this->coreSystemList['config']     = new \BFW\Core\AppSystems\Test\Mock\Config;
        $this->coreSystemList['moduleList'] = new \BFW\Install\Core\AppSystems\Test\Mock\ModuleList;
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

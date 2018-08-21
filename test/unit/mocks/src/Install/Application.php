<?php

namespace BFW\Test\Mock\Install;

//To be included by module who use it
require_once(__DIR__.'/Core/AppSystems/ModuleList.php');
require_once(__DIR__.'/../Core/AppSystems/Config.php');

class Application extends \BFW\Install\Application
{
    protected function defineCoreSystemList()
    {
        parent::defineCoreSystemList();
        
        $this->coreSystemList['config']     = new \BFW\Test\Mock\Core\AppSystems\Config;
        $this->coreSystemList['moduleList'] = new \BFW\Test\Mock\Install\Core\AppSystems\ModuleList;
    }
    
    public function setCoreSystemList(array $coreSystemList): self
    {
        $this->coreSystemList = $coreSystemList;
        return $this;
    }
    
    public function addToCoreSystemList(
        name $name,
        \BFW\Core\AppSystems\SystemInterface $system
    ): self {
        $this->coreSystemList[$name] = $system;
        return $this;
    }

    public function setDeclaredOptions(array $declaredOptions): self
    {
        $this->declaredOptions = $declaredOptions;
        return $this;
    }

    public function setRunTasks(\BFW\RunTasks $runTasks): self
    {
        $this->runTasks = $runTasks;
        return $this;
    }
}

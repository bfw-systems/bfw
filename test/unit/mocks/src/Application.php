<?php

namespace BFW\Test\Mock;

//To be included by module who use it
require_once(__DIR__.'/Core/AppSystems/Cli.php');
require_once(__DIR__.'/Core/AppSystems/Config.php');
require_once(__DIR__.'/Core/AppSystems/Errors.php');
require_once(__DIR__.'/Core/AppSystems/ModuleList.php');

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
    
    public function setCoreSystemList(array $coreSystemList): self
    {
        $this->coreSystemList = $coreSystemList;
        return $this;
    }
    
    public function addToCoreSystemList(
        string $name,
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

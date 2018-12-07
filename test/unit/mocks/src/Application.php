<?php

namespace BFW\Test\Mock;

//To be included by module who use it
require_once(__DIR__.'/Core/AppSystems/Cli.php');
require_once(__DIR__.'/Core/AppSystems/Config.php');
require_once(__DIR__.'/Core/AppSystems/Errors.php');
require_once(__DIR__.'/Core/AppSystems/ModuleList.php');

class Application extends \BFW\Application
{
    protected $appSystemToInstantiate = [];
    
    public function getAppSystemToInstantiate()
    {
        return $this->appSystemToInstantiate;
    }

    public function setAppSystemToInstantiate(array $appSystemToInstantiate)
    {
        $this->appSystemToInstantiate = $appSystemToInstantiate;
        return $this;
    }

    public function addToAppSystemToInstantiate(string $name, string $class)
    {
        $this->appSystemToInstantiate[$name] = $class;
        return $this;
    }
    
    protected function obtainAppSystemList(): array
    {
        if ($this->appSystemToInstantiate !== []) {
            return $this->appSystemToInstantiate;
        }
        
        return $this->obtainAppSystemDefaultList();
    }
    
    public function obtainAppSystemDefaultList(): array
    {
        $list = parent::obtainAppSystemList();
        
        $list['cli']        = '\BFW\Test\Mock\Core\AppSystems\Cli';
        $list['config']     = '\BFW\Test\Mock\Core\AppSystems\Config';
        $list['errors']     = '\BFW\Test\Mock\Core\AppSystems\Errors';
        $list['moduleList'] = '\BFW\Test\Mock\Core\AppSystems\ModuleList';
        
        return $list;
    }
    
    public function obtainParentAppSystemList(): array
    {
        return parent::obtainAppSystemList();
    }
    
    public function setAppSystemList(array $appSystemList): self
    {
        $this->appSystemList = $appSystemList;
        return $this;
    }
    
    public function addToCoreSystemList(
        string $name,
        \BFW\Core\AppSystems\SystemInterface $system
    ): self {
        $this->appSystemList[$name] = $system;
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

<?php

namespace BFW\Install\Core\AppSystems;

use \BFW\Core\AppSystems\AbstractSystem;

class ModuleInstall extends AbstractSystem
{
    use \BFW\Traits\BasicCliMsg;
    
    /**
     * @var \BFW\Install\ModuleInstall[] $listToInstall
     */
    protected $listToInstall = [];
    
    /**
     * {@inheritdoc}
     * 
     * @return $this
     */
    public function __invoke()
    {
        return $this;
    }
    
    /**
     * Getter accessor to property listToInstall
     * 
     * @return \BFW\Install\ModuleInstall[]
     */
    public function getListToInstall(): array
    {
        return $this->listToInstall;
    }
    
    /**
     * {@inheritdoc}
     */
    public function toRun(): bool
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     * Run install of all modules
     */
    public function run()
    {
        $this->installAllModules();
        $this->runStatus = true;
    }
    
    /**
     * Add a new module to the list to install
     * 
     * @param \BFW\Install\ModuleInstall $module The new module
     * 
     * @return $this
     */
    public function addToList(\BFW\Install\ModuleInstall $module): self
    {
        $moduleName = $module->getName();
        
        $this->listToInstall[$moduleName] = $module;
        
        return $this;
    }
    
    /**
     * Install all modules in the order of the dependency tree.
     * 
     * @return void
     */
    protected function installAllModules()
    {
        $this->displayMsgNLInCli('Read all modules to run install script...');
        
        $tree = \BFW\Install\Application::getInstance()
            ->getModuleList()
            ->getLoadTree()
        ;

        foreach ($tree as $firstLine) {
            foreach ($firstLine as $secondLine) {
                foreach ($secondLine as $moduleName) {
                    if (!isset($this->listToInstall[$moduleName])) {
                        continue;
                    }
                    
                    $this->installModule($moduleName);
                }
            }
        }
        
        $this->displayMsgNLInCli('All modules have been read.');
    }
    
    /**
     * Install a module
     * 
     * @param string $moduleName The module name
     * 
     * @return void
     */
    protected function installModule(string $moduleName)
    {
        if (!isset($this->listToInstall[$moduleName])) {
            return;
        }
        
        $this->displayMsgNLInCli(' > Read for module '.$moduleName);
        
        $module         = $this->listToInstall[$moduleName];
        $installScripts = $module->getSourceInstallScript();
        
        if (empty($installScripts) || $installScripts === false) {
            $this->displayMsgNLInCli(' >> No script to run.');
            return;
        }
        
        if (!is_array($installScripts)) {
            $installScripts = [$installScripts];
        }
        
        foreach ($installScripts as $scriptPath) {
            $module->runInstallScript($scriptPath);
        }
    }
}

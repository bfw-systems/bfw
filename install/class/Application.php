<?php

namespace BFW\Install;

use \BFW\Install\ModuleInstall;

/**
 * Application class for install module script
 */
class Application extends \BFW\Application
{
    /**
     * @var \BFW\Install\ModuleInstall[] $modulesInstall Modules to install
     */
    protected static $modulesInstall = [];
    
    /**
     * Getter to static property modulesInstall
     * 
     * @return \BFW\Install\ModuleInstall[]
     */
    public static function getModulesInstall()
    {
        return self::$modulesInstall;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function initRequest()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    protected function initSession()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    protected function initErrors()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    protected function initCli()
    {
        return;
    }
    
    /**
     * Add a new module in the list to install
     * 
     * @param \BFW\Install\ModuleInstall $module
     * 
     * @return void
     */
    public static function addModuleInstall(ModuleInstall $module)
    {
        $moduleName = $module->getName();
        
        self::$modulesInstall[$moduleName] = $module;
    }

    /**
     * {@inheritdoc}
     */
    protected function declareRunSteps()
    {
        $this->runSteps = [
            [$this, 'loadMemcached'],
            [$this, 'loadAllModules'],
            [$this, 'installAllModules']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $runTasks = $this->getSubjectForName('ApplicationTasks');
        
        $runTasks->setNotifyPrefix('BfwAppModulesInstall');
        $runTasks->run();
        $runTasks->sendNotify('bfw_modules_install_done');
    }
    
    /**
     * Install all modules in the order of the dependency tree.
     * 
     * @return void
     */
    protected function installAllModules()
    {
        echo 'Read all modules to run install script :'."\n";
        
        $tree = $this->modules->getLoadTree();

        foreach ($tree as $firstLine) {
            foreach ($firstLine as $secondLine) {
                foreach ($secondLine as $moduleName) {
                    if (!isset(self::$modulesInstall[$moduleName])) {
                        continue;
                    }
                    
                    $this->installModule($moduleName);
                }
            }
        }
    }
    
    /**
     * Install a module
     * 
     * @param string $moduleName The module name
     * 
     * @return void
     */
    protected function installModule($moduleName)
    {
        if (!isset(self::$modulesInstall[$moduleName])) {
            return;
        }
        
        echo ' > Read for module '.$moduleName."\n";
        
        $module         = self::$modulesInstall[$moduleName];
        $installScripts = $module->getSourceInstallScript();
        
        if ($installScripts === '') {
            echo ' >> No script to run.'."\n";
            return;
        }
        
        if (is_string($installScripts)) {
            $installScripts = (array) $installScripts;
        }
        
        foreach ($installScripts as $scriptPath) {
            $module->runInstall($scriptPath);
        }
    }
}

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
            [$this, 'installModules']
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
        $runTasks->sendNotify('bfw_modules_install_finish');
    }
    
    /**
     * Install all modules in the order of the dependency tree.
     * 
     * @return void
     */
    protected function installModules()
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

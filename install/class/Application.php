<?php

namespace BFW\Install;

/**
 * Application class for install module script
 */
class Application extends \BFW\Application
{
    protected static $modulesInstances = [];
    
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
    
    public static function addModuleInstallInstance(
        \BFW\Install\ModuleInstall $module
    ) {
        $moduleName = $module->getName();
        
        self::$modulesInstances[$moduleName] = $module;
    }

    /**
     * {@inheritdoc}
     */
    protected function declareRunPhases()
    {
        $this->runPhases = [
            [$this, 'loadMemcached'],
            [$this, 'readAllModules'],
            [$this, 'installModules']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        foreach ($this->runPhases as $action) {
            $action();

            $notifyAction = $action;
            if (is_array($action)) {
                $notifyAction = $action[1];
            }

            $this->notifyAction('bfw_modules_install_run_'.$notifyAction);
        }

        $this->notifyAction('bfw_modules_install_finish');
    }
    
    protected function installModules()
    {
        echo 'Read all modules to run install script :'."\n";
        
        $tree = $this->modules->getLoadTree();

        foreach ($tree as $firstLine) {
            foreach ($firstLine as $secondLine) {
                foreach ($secondLine as $moduleName) {
                    if (!isset(self::$modulesInstances[$moduleName])) {
                        continue;
                    }
                    
                    $this->installModule($moduleName);
                }
            }
        }
    }
    
    protected function installModule($moduleName)
    {
        if (!isset(self::$modulesInstances[$moduleName])) {
            return;
        }
        
        echo ' > Read for module '.$moduleName."\n";
        
        $module         = self::$modulesInstances[$moduleName];
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

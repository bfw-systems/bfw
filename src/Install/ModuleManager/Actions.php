<?php

namespace BFW\Install\ModuleManager;

use Exception;
use BFW\Install\ModuleManager as Manager;
use BFW\Install\ReadDirLoadModule;
use bultonFr\Utils\Cli\BasicMsg;

class Actions
{
    /**
     * @const EXCEP_MOD_NOT_FOUND Exception code if the asked module has not
     * been found.
     */
    const EXCEP_MOD_NOT_FOUND = 1701001;

    /**
     * The manager which have instancied this class
     *
     * @var \BFW\Install\ModuleManager
     */
    protected $manager;
    
    /**
     * List of all path for all modules found.
     * The key is the module name, the value the absolute path.
     *
     * @var string[string] $modulePathList
     */
    protected $modulePathList = [];
    
    /**
     * List of all module found.
     * The key is the module name, the value the instance of
     * \BFW\Install\ModuleManager\Module for the module.
     *
     * @var \BFW\Install\ModuleManager\Module[string] $moduleList
     */
    protected $moduleList = [];

    /**
     * Constructor
     *
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get the value of $manager
     *
     * @return \BFW\Install\ModuleManager
     */
    public function getManager(): Manager
    {
        return $this->manager;
    }

    /**
     * Get the value of $modulePathList
     *
     * @return string[string]
     */
    public function getModulePathList(): array
    {
        return $this->modulePathList;
    }

    /**
     * Get the value of $moduleList
     *
     * @return \BFW\Install\ModuleManager\Module[string]
     */
    public function getModuleList(): array
    {
        return $this->moduleList;
    }
    
    /**
     * Call the method dedicated to the action.
     * The action is obtain from Manager class.
     *
     * @return void
     */
    public function doAction()
    {
        $actionName = $this->manager->getAction();
        
        if ($actionName === 'add') {
            return $this->doAdd();
        } elseif ($actionName === 'enable') {
            return $this->doEnable();
        } elseif ($actionName === 'disable') {
            return $this->doDisable();
        } elseif ($actionName === 'delete') {
            return $this->doDelete();
        }
    }
    
    /**
     * Run actions to do to prepare and execute add of modules
     * Execute the deleting action if the reinstall option has been declared.
     *
     * @return void
     */
    protected function doAdd()
    {
        if ($this->manager->getReinstall() === true) {
            $this->doDelete();
        }

        $app        = \BFW\Install\Application::getInstance();
        $vendorPath = $app->getOptions()->getValue('vendorDir');
        
        $this->obtainModulePathList($vendorPath);
        $this->executeForModules('doAdd', 'Add');
        
        foreach ($this->moduleList as $module) {
            $this->runInstallScript($module);
        }
    }
    
    /**
     * Run actions to do to prepare and execute enabling of modules
     *
     * @return void
     */
    protected function doEnable()
    {
        $this->obtainModulePathList(MODULES_AVAILABLE_DIR);
        $this->executeForModules('doEnable', 'Enable');
    }
    
    /**
     * Run actions to do to prepare and execute disabling of modules
     *
     * @return void
     */
    protected function doDisable()
    {
        $this->obtainModulePathList(MODULES_AVAILABLE_DIR);
        $this->executeForModules('doDisable', 'Disable');
    }
    
    /**
     * Run actions to do to prepare and execute deleting of modules
     *
     * @return void
     */
    protected function doDelete()
    {
        $this->obtainModulePathList(MODULES_AVAILABLE_DIR);
        $this->executeForModules('doDelete', 'Delete');
    }

    /**
     * obtain all modules present in a directory and add each module to
     * the property $modulePathList.
     *
     * @param string $dirPath
     *
     * @return void
     */
    protected function obtainModulePathList(string $dirPath)
    {
        $listModules = $this->searchAllModulesInDir($dirPath);

        foreach ($listModules as $modulePath) {
            $pathExploded = explode('/', $modulePath);
            $moduleName   = $pathExploded[(count($pathExploded) - 1)];

            $this->modulePathList[$moduleName] = $modulePath;
        }

        ksort($this->modulePathList);
    }

    /**
     * Search all modules present in a directory.
     *
     * @param string $dirPath
     *
     * @return void
     */
    protected function searchAllModulesInDir(string $dirPath)
    {
        $listModules = [];
        $readDir     = new ReadDirLoadModule($listModules);
        $readDir->run($dirPath);

        return $listModules;
    }
    
    /**
     * Call the method actionOnModule for each module find, or only for the
     * module specified on the Manager.
     *
     * @param string $actionMethodName The method to call in Module class
     * @param string $actionName The name of the action (used by log)
     *
     * @return void
     */
    protected function executeForModules(
        string $actionMethodName,
        string $actionName
    ) {
        $specificModule = $this->manager->getSpecificModule();
        if (empty($specificModule) === false) {
            $this->actionOnModule(
                $specificModule,
                '',
                $actionMethodName,
                $actionName
            );
            
            return;
        }
        
        foreach ($this->modulePathList as $moduleName => $modulePath) {
            $this->actionOnModule(
                $moduleName,
                $modulePath,
                $actionMethodName,
                $actionName
            );
        }

        ksort($this->moduleList);
    }
    
    /**
     * Instanciate Module class dedicated for $moduleName, and
     * call $actionMethodName into the Module class to run the action for this
     * module.
     *
     * Some output for shell are displayed from here.
     *
     * @param string $moduleName The module name
     * @param string $modulePath The module path
     * @param string $actionMethodName The method to call in Module class
     * @param string $actionName The name of the action (used by log)
     *
     * @return void
     */
    protected function actionOnModule(
        string $moduleName,
        string $modulePath,
        string $actionMethodName,
        string $actionName
    ) {
        BasicMsg::displayMsg('> '.$actionName.' module '.$moduleName.' ... ', 'yellow');
        
        if (empty($modulePath)) {
            if (!isset($this->modulePathList[$moduleName])) {
                throw new Exception(
                    'The module '.$moduleName.' has not been found in the directory',
                    static::EXCEP_MOD_NOT_FOUND
                );
            }
            
            $modulePath = $this->modulePathList[$moduleName];
        }
        
        try {
            $module = $this->obtainModule($moduleName);
            $module->setVendorPath($modulePath);
            $module->{$actionMethodName}();
        } catch (Exception $e) {
            BasicMsg::displayMsgNL(
                'ERROR #'.$e->getCode().' : '.$e->getMessage(),
                'red',
                'bold'
            );
            
            return;
        }
        
        $this->moduleList[$moduleName] = $module;
        
        BasicMsg::displayMsgNL('Done', 'green');
    }

    /**
     * Instanciate the Module class for the module $moduleName
     *
     * @param string $moduleName The module's name
     *
     * @return \BFW\Install\ModuleManager\Module
     */
    protected function obtainModule(string $moduleName): Module
    {
        return new Module($moduleName);
    }
    
    /**
     * Check if there are an install script for $module, and call the method
     * to run it if there is one.
     *
     * Some output for shell are displayed from here.
     *
     * @param \BFW\Install\ModuleManager\Module $module
     *
     * @return void
     */
    protected function runInstallScript(Module $module)
    {
        BasicMsg::displayMsg(
            '> Execute install script for '.$module->getName().' ... ',
            'yellow'
        );
        
        if ($module->hasInstallScript() === false) {
            BasicMsg::displayMsgNL('No script, pass.', 'yellow');
            return;
        }
        
        try {
            $module->runInstallScript();
        } catch (Exception $e) {
            BasicMsg::displayMsgNL(
                'ERROR #'.$e->getCode().' : '.$e->getMessage(),
                'red',
                'bold'
            );
            
            return;
        }
        
        BasicMsg::displayMsgNL('Done', 'green');
    }
}

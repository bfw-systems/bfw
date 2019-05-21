<?php

namespace BFW\Core\AppSystems;

class ModuleList extends AbstractSystem
{
    /**
     * @var \BFW\Core\ModuleList $moduleList
     */
    protected $moduleList;
    
    /**
     * Initialize the ModuleList system
     */
    public function __construct()
    {
        $this->moduleList = new \BFW\Core\ModuleList;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Core\ModuleList
     */
    public function __invoke()
    {
        return $this->moduleList;
    }

    /**
     * Getter accessor to property moduleList
     * 
     * @return \BFW\Core\ModuleList
     */
    public function getModuleList()
    {
        return $this->moduleList;
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
     * 
     * Load all modules, run all core and app modules
     */
    public function run()
    {
        $this->loadAllModules();
        $this->runAllCoreModules();
        $this->runAllAppModules();
        
        $this->runStatus = true;
    }
    
    /**
     * Read all directories in modules directory and add each module to Modules
     * class.
     * Generate the load tree.
     * Not initialize modules !
     * 
     * @return void
     */
    protected function loadAllModules()
    {
        $listModules = array_diff(scandir(MODULES_ENABLED_DIR), ['.', '..']);

        foreach ($listModules as $moduleName) {
            $modulePath = realpath(MODULES_DIR.$moduleName); //Symlink

            if (!is_dir($modulePath)) {
                continue;
            }

            $this->moduleList->addModule($moduleName);
        }

        $this->moduleList->readNeedMeDependencies();
        $this->moduleList->generateTree();
    }

    /**
     * Load core modules defined into config bfw file.
     * Only module for controller, router, database and template only.
     * 
     * @return void
     */
    protected function runAllCoreModules()
    {
        $allModules = \BFW\Application::getInstance()
            ->getConfig()
            ->getValue('modules', 'modules.php')
        ;
        
        foreach ($allModules as $moduleInfos) {
            $moduleName    = $moduleInfos['name'];
            $moduleEnabled = $moduleInfos['enabled'];

            if (empty($moduleName) || $moduleEnabled === false) {
                continue;
            }

            $this->runModule($moduleName);
        }
    }

    /**
     * Load all modules (except core).
     * Get the load tree, read him and load all modules with the order
     * declared into the tree.
     * 
     * @return void
     */
    protected function runAllAppModules()
    {
        $tree = $this->moduleList->getLoadTree();

        foreach ($tree as $firstLine) {
            foreach ($firstLine as $secondLine) {
                foreach ($secondLine as $moduleName) {
                    $this->runModule($moduleName);
                }
            }
        }
    }

    /**
     * Load a module
     * 
     * @param string $moduleName The module's name to load
     * 
     * @return void
     */
    protected function runModule(string $moduleName)
    {
        $app = \BFW\Application::getInstance();
        
        $app->getSubjectList()
            ->getSubjectByName('ApplicationTasks')
            ->sendNotify('BfwApp_run_module_'.$moduleName);
        
        $this->moduleList
            ->getModuleByName($moduleName)
            ->runModule();
    }
}

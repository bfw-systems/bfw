<?php

namespace BFW\Install;

/**
 * Application class for install module script
 */
class Application extends \BFW\Application
{
    /**
     * {@inheritdoc}
     * Remove not used systems, and add new system used by installer
     */
    protected function defineCoreSystemList()
    {
        parent::defineCoreSystemList();
        
        //Remove not used systems
        unset(
            $this->coreSystemList['request'],
            $this->coreSystemList['session'],
            $this->coreSystemList['errors'],
            $this->coreSystemList['cli']
        );
        
        //Change ModuleList class
        $this->coreSystemList['moduleList'] = new Core\AppSystems\ModuleList;
        
        //Add new system : module installation system
        $this->coreSystemList['moduleInstall'] = new Core\AppSystems\ModuleInstall;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->getMonolog()
            ->getLogger()
            ->debug('running framework install')
        ;
        
        $this->runTasks->run();
        $this->runTasks->sendNotify('bfw_modules_install_done');
    }
}

<?php

namespace BFW\Install;

/**
 * Application class for install module script
 * 
 * @method \BFW\Install\Core\AppSystems\ModuleManager getModuleManager()
 */
class Application extends \BFW\Application
{
    /**
     * {@inheritdoc}
     * Remove not used systems, and add new system used by installer
     */
    protected function obtainAppSystemList(): array
    {
        $appSystemList = parent::obtainAppSystemList();
        
        //Remove not used systems
        unset(
            $appSystemList['request'],
            $appSystemList['session'],
            $appSystemList['errors'],
            $appSystemList['ctrlRouterLink']
        );
        
        $appSystemNS = '\BFW\Install\Core\AppSystems\\';
        
        //Change ModuleList class
        $appSystemList['moduleList'] = $appSystemNS.'ModuleList';
        
        //Add new system : module installation system
        $appSystemList['moduleManager'] = $appSystemNS.'ModuleManager';
        
        return $appSystemList;
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
        $this->runTasks->sendNotify('bfw_install_done');
    }
}

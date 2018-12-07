<?php

namespace BFW\Install;

/**
 * Application class for install module script
 * 
 * @method \BFW\Install\Core\AppSystems\ModuleInstall getModuleInstall()
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
            $appSystemList['cli'],
            $appSystemList['ctrlRouterLink']
        );
        
        $appSystemNS = '\BFW\Install\Core\AppSystems\\';
        
        //Change ModuleList class
        $appSystemList['moduleList'] = $appSystemNS.'ModuleList';
        
        //Add new system : module installation system
        $appSystemList['moduleInstall'] = $appSystemNS.'ModuleInstall';
        
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
        $this->runTasks->sendNotify('bfw_modules_install_done');
    }
}

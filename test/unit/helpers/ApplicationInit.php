<?php

namespace BFW\test\helpers;

//To have the mock loaded for external module which use this class.
require_once(__DIR__.'/../mocks/src/class/Application.php');
require_once(__DIR__.'/../mocks/src/class/Modules.php');

/**
 * Application mock used by external modules to initialize Application class
 */
class ApplicationInit extends \BFW\test\unit\mocks\Application
{
    /**
     * {@inheritdoc}
     * Add options : 
     * * forceConfig : Array which remplace BFW config file
     * * overrideMethods : list of Application method which be overrided
     *    If the value is callable, the callable will be call
     *    Else, if it's a data, the data will be returned by the method
     */
    protected function initSystem($options)
    {
        $forcedConfig = require(__DIR__.'/applicationConfig.php');
        
        if (isset($options['forceConfig'])) {
            $forcedConfig = array_merge(
                $forcedConfig,
                (array) $options['forceConfig']
            );
        }
        
        $forcedOptions = [
            'forceConfig'     => $forcedConfig,
            'vendorDir'       => __DIR__.'/../../../vendor',
            'testOption'      => 'unit test',
            'overrideMethods' => [
                'runCliFile'     => null,
                'initModules'    => function() {
                    $this->modules = new \BFW\test\unit\mocks\Modules;
                },
                'readAllModules' => function() {
                    $modules = $this->modules;
                    foreach($this->modulesToAdd as $moduleName => $module) {
                        $modules::declareModuleConfig($moduleName, $module->config);
                        $modules::declareModuleLoadInfos($moduleName, $module->loadInfos);
                        
                        $this->modules->addModule($moduleName);
                    }
                    
                    $this->modules->readNeedMeDependencies();
                    $this->modules->generateTree();
                }
            ]
        ];
            
        $forcedOptions = array_merge($forcedOptions, (array) $options);
        
        parent::initSystem($forcedOptions);
    }
}

<?php

namespace BFW\test\helpers;

class ApplicationInit extends \BFW\test\unit\mocks\Application
{
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

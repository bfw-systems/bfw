<?php

namespace BFW\test\helpers;

trait Application
{
    public static function initApp($config = [], $options = [])
    {
        $forcedConfig = require(__DIR__.'/applicationConfig.php');
        $forcedConfig = array_merge((array) $config, $forcedConfig);
        
        $vendorPath = __DIR__.'/../../../vendor';
        if (strpos(__DIR__, 'vendor') !== false) {
            $vendorPath = __DIR__.'/../../../../..';
        }
        
        $forcedOptions = [
            'forceConfig'     => $forcedConfig,
            'vendorDir'       => __DIR__.'/../../../../vendor',
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
            
        $forcedOptions = array_merge((array) $options, $forcedOptions);
        
        return \BFW\test\unit\mocks\Application::init($forcedOptions);
    }
}

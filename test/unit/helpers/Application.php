<?php

namespace BFW\test\helpers;

/**
 * If you want use this trait from a external script, because composer not
 * load class in "autoload-dev" section, you should manual include : 
 * * /vendor/bulton-fr/bfw/test/unit/mocks/src/class/ApplicationForceConfig.php
 * * /vendor/bulton-fr/bfw/test/unit/mocks/src/class/Application.php
 * * /vendor/bulton-fr/bfw/test/unit/mocks/src/class/ConfigForceDatas.php
 * * /vendor/bulton-fr/bfw/test/unit/mocks/src/class/Modules.php
 */

trait Application
{
    protected function initApp($sqlSecureMethod)
    {
        $forcedConfig = require(__DIR__.'/applicationConfig.php');
        $forcedConfig['sqlSecureMethod'] = $sqlSecureMethod;
        
        $vendorPath = __DIR__.'/../../../vendor';
        if (strpos(__DIR__, 'vendor') !== false) {
            $vendorPath = __DIR__.'/../../../../..';
        }
        
        $options = [
            'forceConfig' => $forcedConfig,
            'vendorDir'   => $vendorPath
        ];
        
        $this->function->scandir = ['.', '..'];
        \BFW\test\unit\mocks\Application::init($options);
    }
}

<?php

namespace BFW\Install\Test\Helpers;

trait Application
{
    /**
     * @var \BFW\Install\Test\Mock\Application $app
     */
    protected $app;
    
    /**
     * Create the bfw Application instance used by the install system
     * 
     * @return void
     */
    protected function createApp()
    {
        $mockedConfigValues = require(
            realpath(__DIR__.'/../../../../skel/app/config/bfw/config.php')
        );
        
        $this->app = \BFW\Install\Test\Mock\Application::getInstance();
        $this->app->setMockedConfigValues($mockedConfigValues);
    }
    
    /**
     * Call the method initSystem of the bfw Application class
     * 
     * @param boolean $runSession (default false)
     * 
     * @return void
     */
    protected function initApp($runSession = false)
    {
        $this->app->initSystem([
            'rootDir'    => realpath(__DIR__.'/../../../..'),
            'vendorDir'  => realpath(__DIR__.'/../../../../vendor'),
            'runSession' => $runSession
        ]);
    }
}

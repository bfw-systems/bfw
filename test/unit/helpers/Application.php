<?php

namespace BFW\Test\Helpers;

//To be included by module who use it
require_once(__DIR__.'/../mocks/src/class/Application.php');

trait Application
{
    /**
     * @var \BFW\Test\Mock\Application $app 
     */
    protected $app;
    
    /**
     * Create the bfw Application instance
     * 
     * @return void
     */
    protected function createApp()
    {
        $mockedConfigValues = require(
            realpath(__DIR__.'/../../../skel/app/config/bfw/config.php')
        );
        
        $this->app = \BFW\Test\Mock\Application::getInstance();
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
            'rootDir'    => realpath(__DIR__.'/../../..'),
            'vendorDir'  => realpath(__DIR__.'/../../../vendor'),
            'runSession' => $runSession
        ]);
    }
}

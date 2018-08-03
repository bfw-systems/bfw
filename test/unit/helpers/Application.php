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
     * @var string $rootDir : The root directory path of the application
     */
    protected $rootDir;
    
    /**
     * Setter accessor for rootDir property
     * 
     * @param string $rootDir
     * 
     * @return $this
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
        return $this;
    }
    
    /**
     * Create the bfw Application instance
     * 
     * @return void
     */
    protected function createApp()
    {
        $this->app = \BFW\Test\Mock\Application::getInstance();
        $appConfig = $this->app->getCoreSystemList()['config'];
        
        $configFileList = [
            'errors.php',
            'global.php',
            'memcached.php',
            'modules.php',
            'monolog.php'
        ];
        
        foreach ($configFileList as $filename) {
            $configValue = require(
                realpath(__DIR__.'/../../../skel/app/config/bfw/'.$filename)
            );
            
            if ($filename === 'monolog.php') {
                //1.x Monolog always send to stdout if no handler is define :/
                $configValue->handlers[] = (object) [
                    'name' => '\Monolog\Handler\TestHandler',
                    'args' => []
                ];
            }
            
            $appConfig->setMockedList($filename, $configValue);
        }
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
        $this->app->initSystems([
            'rootDir'    => realpath($this->rootDir),
            'vendorDir'  => realpath($this->rootDir.'/vendor'),
            'runSession' => $runSession
        ]);
    }
}

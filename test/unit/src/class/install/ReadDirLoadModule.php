<?php

namespace BFW\Install\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class ReadDirLoadModule extends atoum
{
    //use \BFW\Install\Test\Helpers\Application;
    
    protected $mock;
    protected $listFiles = [];
    
    public function beforeTestMethod($testMethod)
    {
        //$this->createApp();
        //$this->initApp(); //Need constants
        
        $this->mockGenerator
            ->makeVisible('fileAction')
            ->generate('BFW\Install\ReadDirLoadModule')
        ;
        
        $this->mock = new \mock\BFW\Install\ReadDirLoadModule(
            $this->listFiles
        );
    }
    
    public function testFileAction()
    {
        $this->assert('test Helpers\ReadDirectory::fileAction for parent returned value')
            ->string($this->invoke($this->mock)->fileAction('.', __DIR__))
                ->isEqualTo('continue')
            ->array($this->mock->getList())
                ->isEmpty()
            ->string($this->invoke($this->mock)->fileAction('..', __DIR__))
                ->isEqualTo('continue')
            ->array($this->mock->getList())
                ->isEmpty()
        ;
        
        $this->assert('test Helpers\ReadDirectory::fileAction for a random file')
            ->string($this->invoke($this->mock)->fileAction('Application.php', __DIR__))
                ->isEmpty()
            ->array($this->mock->getList())
                ->isEmpty()
        ;
        
        $this->assert('test Helpers\ReadDirectory::fileAction for the bfwModulesInfos.json file')
            ->string($this->invoke($this->mock)->fileAction('bfwModulesInfos.json', __DIR__))
                ->isEqualTo('break')
            ->array($this->mock->getList())
                ->isEqualTo([
                    0 => __DIR__
                ])
        ;
    }
}
<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Request extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mock = new \mock\BFW\Core\AppSystems\Request;
        
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        
        //Remove from the list used by initApp() because request is singleton.
        $coreSystemList = $this->app->getCoreSystemList();
        unset($coreSystemList['request']);
        $this->app->setCoreSystemList($coreSystemList);
        
        $this->initApp();
    }
    
    public function testInit()
    {
        $this->assert('test Core\AppSystems\Request::isInit before init')
            ->boolean($this->mock->isInit())
                ->isFalse()
        ;
        
        $this->assert('test Core\AppSystems\Request::init and isInit after')
            ->variable($this->mock->init())
                ->isNull()
            ->object($this->mock->getRequest())
                ->isInstanceOf('\BFW\Request')
            ->boolean($this->mock->isInit())
                ->isTrue()
            ->variable($this->mock->getRequest()->getIp())
                ->isNotNull() //runDetect has been executed
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\Request::__invoke')
            ->if($this->mock->init())
            ->then
            ->object($this->mock->__invoke())
                ->isIdenticalTo($this->mock->getRequest())
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Core\AppSystems\Request::toRun')
            ->boolean($this->mock->toRun())
                ->isFalse()
        ;
    }
}
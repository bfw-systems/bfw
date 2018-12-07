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
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        
        //Remove from the list used by initApp() because request is singleton.
        $appSystemList = $this->app->obtainAppSystemDefaultList();
        unset($appSystemList['request']);
        $this->app->setAppSystemToInstantiate($appSystemList);
        
        $this->initApp();
        
        if ($testMethod === 'testConstructor') {
            return;
        }
        
        $this->mock = new \mock\BFW\Core\AppSystems\Request;
    }
    
    public function testConstructor()
    {
        $this->assert('test Core\AppSystems\Request::__construct')
            ->given($this->mock = new \mock\BFW\Core\AppSystems\Request)
            ->then
            ->object($this->mock->getRequest())
                ->isInstanceOf('\BFW\Request')
            ->variable($this->mock->getRequest()->getIp())
                ->isNotNull() //runDetect has been executed
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\Request::__invoke')
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
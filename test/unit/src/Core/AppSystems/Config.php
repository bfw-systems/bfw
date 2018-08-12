<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Config extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mock = new \mock\BFW\Core\AppSystems\Config;
        
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();
    }
    
    public function testInit()
    {
        $this->assert('test Core\AppSystems\Config::isInit before init')
            ->boolean($this->mock->isInit())
                ->isFalse()
        ;
        
        $this->assert('test Core\AppSystems\Config::init and isInit after')
            ->variable($this->mock->init())
                ->isNull()
            ->object($this->mock->getConfig())
                ->isInstanceOf('\BFW\Config')
            ->boolean($this->mock->isInit())
                ->isTrue()
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\Config::__invoke')
            ->if($this->mock->init())
            ->then
            ->object($this->mock->__invoke())
                ->isIdenticalTo($this->mock->getConfig())
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Core\AppSystems\Config::toRun')
            ->boolean($this->mock->toRun())
                ->isFalse()
        ;
    }
}
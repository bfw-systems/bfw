<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Monolog extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mock = new \mock\BFW\Core\AppSystems\Monolog;
        
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();
    }
    
    public function testInit()
    {
        $this->assert('test Core\AppSystems\Monolog::isInit before init')
            ->boolean($this->mock->isInit())
                ->isFalse()
        ;
        
        $this->assert('test Core\AppSystems\Monolog::init and isInit after')
            ->variable($this->mock->init())
                ->isNull()
            ->object($this->mock->getMonolog())
                ->isInstanceOf('\BFW\Monolog')
            ->boolean($this->mock->isInit())
                ->isTrue()
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\Monolog::__invoke')
            ->if($this->mock->init())
            ->then
            ->object($this->mock->__invoke())
                ->isIdenticalTo($this->mock->getMonolog())
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Core\AppSystems\Monolog::toRun')
            ->boolean($this->mock->toRun())
                ->isFalse()
        ;
    }
}
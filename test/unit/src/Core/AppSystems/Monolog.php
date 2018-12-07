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
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();
        
        if ($testMethod === 'testConstructor') {
            return;
        }
        
        $this->mock = new \mock\BFW\Core\AppSystems\Monolog;
    }
    
    public function testConstructor()
    {
        $this->assert('test Core\AppSystems\Monolog::__construct')
            ->given($this->mock = new \mock\BFW\Core\AppSystems\Monolog)
            ->then
            ->object($this->mock->getMonolog())
                ->isInstanceOf('\BFW\Monolog')
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\Monolog::__invoke')
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
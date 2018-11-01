<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Errors extends atoum
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
        
        $this->mock = new \mock\BFW\Core\AppSystems\Errors;
    }
    
    public function testInit()
    {
        $this->assert('test Core\AppSystems\Errors::__construct')
            ->given($this->mock = new \mock\BFW\Core\AppSystems\Errors)
            ->then
            ->object($this->mock->getErrors())
                ->isInstanceOf('\BFW\Core\Errors')
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\Errors::__invoke')
            ->object($this->mock->__invoke())
                ->isIdenticalTo($this->mock->getErrors())
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Core\AppSystems\Errors::toRun')
            ->boolean($this->mock->toRun())
                ->isFalse()
        ;
    }
}
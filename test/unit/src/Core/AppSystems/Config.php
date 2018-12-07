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
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();
        
        if ($testMethod === 'testConstructor') {
            return;
        }
        
        $this->mock = new \mock\BFW\Core\AppSystems\Config;
    }
    
    public function testConstructor()
    {
        $this->assert('test Core\AppSystems\Config::__construct')
            ->given($this->mock = new \mock\BFW\Core\AppSystems\Config)
            ->object($this->mock->getConfig())
                ->isInstanceOf('\BFW\Config')
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\Config::__invoke')
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
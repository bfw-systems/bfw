<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Options extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('obtainDefaultOptions')
        ;
        
        $this->mock = new \mock\BFW\Core\AppSystems\Options;
        
        $this->setRootDir(__DIR__.'/../../../../../..');
        $this->createApp();
        $this->initApp();
    }
    
    public function testInit()
    {
        $this->assert('test Core\AppSystems\Options::isInit before init')
            ->boolean($this->mock->isInit())
                ->isFalse()
        ;
        
        $this->assert('test Core\AppSystems\Options::init and isInit after')
            ->variable($this->mock->init())
                ->isNull()
            ->object($this->mock->getOptions())
                ->isInstanceOf('\BFW\Core\Options')
            ->boolean($this->mock->isInit())
                ->isTrue()
            ->string($this->mock->getOptions()->getValue('rootDir'))
                ->isNotEmpty()
            ->string($this->mock->getOptions()->getValue('vendorDir'))
                ->isNotEmpty()
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\Options::__invoke')
            ->if($this->mock->init())
            ->then
            ->object($this->mock->__invoke())
                ->isIdenticalTo($this->mock->getOptions())
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Core\AppSystems\Options::toRun')
            ->boolean($this->mock->toRun())
                ->isFalse()
        ;
    }
    
    public function testObtainDefaultOptions()
    {
        $this->assert('test Core\AppSystems\Options::obtainDefaultOptions')
            ->array($this->mock->obtainDefaultOptions())
                ->isEqualTo([
                    'rootDir'    => null,
                    'vendorDir'  => null,
                    'runSession' => true
                ])
        ;
    }
}
<?php

namespace BFW\Install\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class ModuleManager extends atoum
{
    use \BFW\Test\Helpers\OutputBuffer;

    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('obtainActionClass')
            ->generate('BFW\Install\ModuleManager')
        ;

        $this->mock = new \mock\BFW\Install\ModuleManager;
    }
    
    public function testGetAndSetAction()
    {
        $this->assert('test Install\ModuleManager::getAction for default value')
            ->string($this->mock->getAction())
                ->isEmpty()
        ;

        $this->assert('test Install\ModuleManager::setAction and getAction')
            ->object($this->mock->setAction('add'))
                ->isIdenticalTo($this->mock)
            ->string($this->mock->getAction())
                ->isEqualTo('add')
        ;
    }
    
    public function testGetAndSetReinstall()
    {
        $this->assert('test Install\ModuleManager::getReinstall for default value')
            ->boolean($this->mock->getReinstall())
                ->isFalse()
        ;

        $this->assert('test Install\ModuleManager::setReinstall and getReinstall')
            ->object($this->mock->setReinstall(true))
                ->isIdenticalTo($this->mock)
            ->boolean($this->mock->getReinstall())
                ->isTrue()
            ->object($this->mock->setReinstall(false))
                ->isIdenticalTo($this->mock)
            ->boolean($this->mock->getReinstall())
                ->isFalse()
        ;
    }
    
    public function testGetAndSetAllModules()
    {
        $this->assert('test Install\ModuleManager::getAllModules for default value')
            ->boolean($this->mock->getAllModules())
                ->isFalse()
        ;

        $this->assert('test Install\ModuleManager::setAllModules and getAllModules')
            ->object($this->mock->setAllModules(true))
                ->isIdenticalTo($this->mock)
            ->boolean($this->mock->getAllModules())
                ->isTrue()
            ->object($this->mock->setAllModules(false))
                ->isIdenticalTo($this->mock)
            ->boolean($this->mock->getAllModules())
                ->isFalse()
        ;
    }
    
    public function testGetAndSetSpecificModule()
    {
        $this->assert('test Install\ModuleManager::getSpecificModule for default value')
            ->string($this->mock->getSpecificModule())
                ->isEmpty()
        ;

        $this->assert('test Install\ModuleManager::setSpecificModule and getSpecificModule')
            ->object($this->mock->setSpecificModule('bfw-hello-world'))
                ->isIdenticalTo($this->mock)
            ->string($this->mock->getSpecificModule())
                ->isEqualTo('bfw-hello-world')
        ;
    }
    
    public function testDoAction()
    {
        $this->assert('test Install\ModuleManager::doAction - without error')
            ->given($mockedManager = new class($this->mock) extends \BFW\Install\ModuleManager\Actions {
                public $doActionCalled = false;

                public function doAction()
                {
                    $this->doActionCalled = true;
                }
            })
            ->and($this->calling($this->mock)->obtainActionClass = $mockedManager)
            ->then

            ->variable($this->mock->doAction())
                ->isNull()
            ->boolean($mockedManager->doActionCalled)
                ->isTrue()
        ;

        $this->assert('test Install\ModuleManager::doAction - with error')
            ->given($flushedMsg = '')
            ->and($this->defineOutputBuffer($flushedMsg))
            ->then

            ->given($mockedManager = new class($this->mock) extends \BFW\Install\ModuleManager\Actions {
                public $doActionCalled = false;

                public function doAction()
                {
                    $this->doActionCalled = true;
                    throw new \Exception('for unit test', 9);
                }
            })
            ->and($this->calling($this->mock)->obtainActionClass = $mockedManager)
            ->then

            ->variable($this->mock->doAction())
                ->isNull()
            ->boolean($mockedManager->doActionCalled)
                ->isTrue()
            ->string($flushedMsg)
                ->isEqualTo("\033[1;31mError #9 : for unit test\033[0m\n")
        ;
    }

    public function testObtainActionClass()
    {
        $this->assert('test Install\ModuleManager::obtainActionClass')
            ->object($this->mock->obtainActionClass())
                ->isInstanceOf('\BFW\Install\ModuleManager\Actions')
        ;
    }
}

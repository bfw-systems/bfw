<?php

namespace BFW\Install\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class ModuleManager extends atoum
{
    use \BFW\Test\Helpers\Install\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../../../..');
        $this->createApp();

        $appSystemList = $this->app->obtainAppSystemDefaultList();
        unset($appSystemList['moduleManager']);
        $this->app->setAppSystemToInstantiate($appSystemList);
        
        $this->initApp();
        
        if ($testMethod === 'testConstructor') {
            return;
        }

        $this->mock = new \mock\BFW\Install\Core\AppSystems\ModuleManager;
    }
    
    public function testConstructor()
    {
        $this->assert('test Core\AppSystems\ComposerLoader::__construct')
            ->given($this->mock = new \mock\BFW\Install\Core\AppSystems\ModuleManager)
            ->object($this->mock->getManager())
                ->isInstanceOf('\BFW\Install\ModuleManager')
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Install\Core\AppSystems\ModuleManager::__invoke')
            ->object($this->mock->__invoke())
                ->isInstanceOf('\BFW\Install\ModuleManager')
                ->isIdenticalTo($this->mock->getManager())
        ;
    }
    
    public function testGetManager()
    {
        $this->assert('test Install\Core\AppSystems\ModuleManager::getManager')
            ->object($this->mock->getManager())
                ->isInstanceOf('\BFW\Install\ModuleManager')
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Install\Core\AppSystems\ModuleManager::toRun')
            ->boolean($this->mock->toRun())
                ->isTrue()
        ;
    }
    
    public function testRunAndIsRun()
    {
        $this->assert('test Install\Core\AppSystems\ModuleManager::isRun before run')
            ->boolean($this->mock->isRun())
                ->isFalse()
        ;
        
        $this->assert('test Install\Core\AppSystems\ModuleManager::run and isRun after')
            ->given($setManager = function ($manager) {
                $this->manager = $manager;
            })
            ->and($setManager = $setManager->bindTo($this->mock, $this->mock))
            ->then
            ->given($mockedManager = new class() {
                public $doActionCalled = false;

                public function doAction()
                {
                    $this->doActionCalled = true;
                }
            })
            ->and($setManager($mockedManager))
            ->then

            ->variable($this->mock->run())
                ->isNull()
            ->boolean($this->mock->isRun())
                ->isTrue()
            ->boolean($mockedManager->doActionCalled)
                ->isTrue()
        ;
    }
}

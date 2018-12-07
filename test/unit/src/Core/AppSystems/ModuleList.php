<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

use \BFW\Test\Mock\Core\AppSystems\ModuleList as MockModuleList;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class ModuleList extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('loadAllModules')
            ->makeVisible('runAllCoreModules')
            ->makeVisible('runAllAppModules')
            ->makeVisible('runModule')
        ;
        
        if ($testMethod === 'testConstructor') {
            return;
        }
        
        if (
            $testMethod === 'testLoadAllModulesWithoutModules' ||
            $testMethod === 'testLoadAllModulesWithoutFailedModule' ||
            $testMethod === 'testLoadAllModulesWithFailedModule' ||
            $testMethod === 'testRunAllCoreModules' ||
            $testMethod === 'testRunAllAppModules' ||
            $testMethod === 'testRunModule'
        ) {
            $this->setRootDir(__DIR__.'/../../../../..');
            $this->createApp();
            $this->initApp();
            
            $this->mock = new \mock\BFW\Test\Mock\Core\AppSystems\ModuleList;
        } else {
            $this->mock = new \mock\BFW\Core\AppSystems\ModuleList;
        }
    }
    
    public function testInit()
    {
        $this->assert('test Core\AppSystems\ModuleList::__construct')
            ->given($this->mock = new \mock\BFW\Core\AppSystems\ModuleList)
            ->then
            ->object($this->mock->getModuleList())
                ->isInstanceOf('\BFW\Core\ModuleList')
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\ModuleList::__invoke')
            ->object($this->mock->__invoke())
                ->isIdenticalTo($this->mock->getModuleList())
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Core\AppSystems\ModuleList::toRun')
            ->boolean($this->mock->toRun())
                ->isTrue()
        ;
    }
    
    public function testRunAndIsRun()
    {
        $this->assert('test Core\AppSystems\ModuleList::isRun before run')
            ->boolean($this->mock->isRun())
                ->isFalse()
        ;
        
        $this->assert('test Core\AppSystems\ModuleList::run and isRun after')
            ->and($this->calling($this->mock)->loadAllModules = null)
            ->and($this->calling($this->mock)->runAllCoreModules = null)
            ->and($this->calling($this->mock)->runAllAppModules = null)
            ->variable($this->mock->run())
                ->isNull()
            ->boolean($this->mock->isRun())
                ->isTrue()
            ->mock($this->mock)
                ->call('loadAllModules')
                    ->once()
                ->call('runAllCoreModules')
                    ->once()
                ->call('runAllAppModules')
                    ->once()
        ;
    }
    
    protected function addModule($moduleName, $isCore = false)
    {
        $this
            //Add the module to the mocked list
            ->if(MockModuleList::addToMockedList(
                $moduleName,
                (object) [
                    'config'    => (object) [],
                    'loadInfos' => (object) []
                ]
            ))
        ;
        
        if ($isCore === true) {
            $this
                //Mock the config to add a core module
                ->given($config = $this->app->getConfig())
                ->given($mockedConfig = $config->getConfigByFilename('modules.php'))
                ->if($mockedConfig['modules']['controller']['name'] = $moduleName)
                ->and($mockedConfig['modules']['controller']['enabled'] = true)
                ->and($config->setConfigForFilename('modules.php', $mockedConfig))
            ;
        }
        
        $this->moduleMockNativeFunctions($moduleName);
        
        return $this;
    }
    
    /**
     * Mock php native function used by readAllModules()
     * 
     * @param type $moduleName
     * @return type
     */
    protected function moduleMockNativeFunctions($moduleName = null)
    {
        if (is_null($moduleName)) {
            $this->function->scandir = ['.', '..'];
            return $this;
        }
        
        $this->function->scandir  = ['.', '..', $moduleName];
        $this->function->realpath = $moduleName;
        $this->function->is_dir   = true;
        
        return $this;
    }
    
    /**
     * Test method for readAllModules() when there is no declared modules.
     * 
     * @return void
     */
    public function testLoadAllModulesWithoutModules()
    {
        $this->assert('test Core\AppSystems\ModuleList::loadAllModules without modules')
            ->if($this->moduleMockNativeFunctions())
            ->then
            ->variable($this->mock->loadAllModules())
                ->isNull()
            ->array($this->mock->getModuleList()->getLoadTree())
                ->size
                    ->isEqualTo(0);
    }
    
    /**
     * Test method for readAllModules() when there is one module without fail
     * 
     * @return void
     */
    public function testLoadAllModulesWithoutFailedModule()
    {
        $this->assert('test Core\AppSystems\ModuleList::loadAllModules with one module')
            ->given($this->addModule('test1'))
            ->then
            
            ->variable($this->mock->loadAllModules())
                ->isNull()
            ->array($this->mock->getModuleList()->getLoadTree())
                ->size
                    ->isGreaterThan(0)
            ->object($this->mock->getModuleList()->getModuleByName('test1'))
                //Not an exception because unknown module :)
        ;
    }
    
    /**
     * Test method for readAllModules() when there is one module with fail
     * 
     * @return void
     */
    public function testLoadAllModulesWithFailedModule()
    {
        $this->assert('test Core\AppSystems\ModuleList::loadAllModules with one module')
            ->given($this->addModule('test1'))
            ->and($this->function->is_dir = false) //<--- Not a dir. => Fail
            ->then
            
            ->variable($this->mock->loadAllModules())
                ->isNull()
            ->array($this->mock->getModuleList()->getLoadTree())
                ->size
                    ->isEqualTo(0);
    }
    
    public function testRunAllCoreModules()
    {
        $this->assert('test Core\AppSystems\ModuleList::runAllCoreModules')
            ->given($this->addModule('test1', true))
            ->and($this->mock->loadAllModules())
            ->then
            
            ->variable($this->mock->runAllCoreModules())
                ->isNull()
            ->variable($module = $this->mock->getModuleList()->getModuleByName('test1'))
                ->isNotNull()
            ->boolean($module->isLoaded())
                ->isTrue()
            ->boolean($module->isRun())
                ->isTrue()
        ;
    }
    
    public function testRunAllAppModules()
    {
        $this->assert('test Core\AppSystems\ModuleList::runAllAppModules')
            ->given($this->addModule('test1'))
            ->and($this->mock->loadAllModules())
            ->then
            
            ->variable($this->mock->runAllAppModules())
                ->isNull()
            ->variable($module = $this->mock->getModuleList()->getModuleByName('test1'))
                ->isNotNull()
            ->boolean($module->isLoaded())
                ->isTrue()
            ->boolean($module->isRun())
                ->isTrue()
        ;
    }
    
    public function testRunModule()
    {
        $this->assert('test Core\AppSystems\ModuleList::runModule')
            ->given($this->addModule('test1'))
            ->and($this->mock->loadAllModules())
            ->then
            
            //Define observer
            ->given($observer = new \BFW\Test\Helpers\ObserverArray())
            ->if($subject = $this->app->getSubjectList()->getSubjectByName('ApplicationTasks'))
            ->and($subject->attach($observer))
            ->then
            
            ->variable($this->mock->runModule('test1'))
                ->isNull()
            ->variable($module = $this->mock->getModuleList()->getModuleByName('test1'))
                ->isNotNull()
            ->boolean($module->isLoaded())
                ->isTrue()
            ->boolean($module->isRun())
                ->isTrue()
            ->array($observer->getActionReceived())
                ->contains('BfwApp_run_module_test1')
        ;
    }
}
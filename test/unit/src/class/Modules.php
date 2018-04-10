<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Modules extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->createApp();
        $this->initApp();
        
        if ($testMethod == 'testConstruct') {
            return;
        }
        
        $this->mock = new \mock\BFW\Test\Mock\Modules;
    }
    
    public function testGetModules()
    {
        $this->assert('test Modules::getModules without module')
            ->array($this->mock->getModules())
                ->isEmpty()
        ;
        
        //add with extended method, not test method \BFW\Modules::addModule().
        $this->assert('test Modules::addModule')
            ->variable($this->mock->addModule('atoum'))
                ->isNull()
        ;
        
        $this->assert('test Modules::getModules with a module')
            ->array($modules = $this->mock->getModules())
                ->isNotEmpty()
                ->hasKey('atoum')
            ->object($modules['atoum'])
                ->isInstanceOf('\BFW\Module')
        ;
    }
    
    public function testAddModule()
    {
        //I don't want to mock native function used into Module::loadJsonFile.
        //So not tested here.
        //Tested with bin test with the module hello-world ;)
    }
    
    public function testGetModuleForName()
    {
        $this->assert('test Modules::getModuleForName with not existing module')
            ->exception(function() {
                $this->mock->getModuleForName('atoum');
            })
                ->hasCode(\BFW\Modules::ERR_NOT_FOUND)
        ;
        
        $this->assert('test Modules::getModuleForName with an existing module')
            ->if($this->mock->addModule('atoum'))
            ->then
            ->object($this->mock->getModuleForName('atoum'))
                ->isInstanceOf('\BFW\Module')
        ;
    }
    
    public function testReadNeedMeDependencies()
    {
        $mock = $this->mock;
        
        $this->assert('test Modules::readNeedMeDependencies with a module which not have needMe property')
            ->if($mock::setModuleLoadInfos('atoum', new \stdClass))
            ->and($this->mock->addModule('atoum'))
            ->given($module = clone $this->mock->getModuleForName('atoum'))
            ->then
            ->variable($this->mock->readNeedMeDependencies())
                ->isNull()
            ->object($this->mock->getModuleForName('atoum'))
                ->isEqualTo($module)
        ;
        
        $this->assert('test Modules::readNeedMeDependencies with a dependency')
            ->if($mock::setModuleLoadInfos(
                'hello-world',
                (object) ['needMe' => 'atoum']
            ))
            ->and($this->mock->addModule('hello-world'))
            ->given($moduleAtoum = clone $this->mock->getModuleForName('atoum'))
            ->given($moduleHelloWorld = clone $this->mock->getModuleForName('hello-world'))
            ->then
            ->variable($this->mock->readNeedMeDependencies())
                ->isNull()
            ->object($this->mock->getModuleForName('atoum'))
            //We don't care about what is changed, it's for Module unit test.
            //But we need to check if something as changed.
                ->isNotEqualTo($moduleAtoum)
            ->object($this->mock->getModuleForName('hello-world'))
                ->isEqualTo($moduleHelloWorld)
        ;
        
        $this->assert('test Modules::readNeedMeDependencies with a not existing dependency')
            ->if($mock::setModuleLoadInfos(
                'api',
                (object) ['needMe' => 'auth']
            ))
            ->and($this->mock->addModule('api'))
            ->then
            ->exception(function() {
                $this->mock->readNeedMeDependencies();
            })
                ->hasCode(\BFW\Modules::ERR_NEEDED_NOT_FOUND)
        ;
    }
    
    public function testGenerateTreeAndGetLoadTree()
    {
        $this->assert('test Modules::getLoadTree for default value')
            ->array($this->mock->getLoadTree())
                ->isEmpty()
        ;
        
        $this->assert('test Modules::generateTree without module')
            ->variable($this->mock->generateTree())
                ->isNull()
            ->array($this->mock->getLoadTree())
                ->isEmpty()
        ;
        
        $this->assert('test Modules::generateTree with some modules')
            ->given($mock = $this->mock)
            ->and($mock::setModuleLoadInfos('atoum', (object) [
                'priority' => 1
            ]))
            ->and($mock::setModuleLoadInfos(
                'hello-world',
                (object) [
                    'require'  => 'atoum',
                    'priority' => 0 //So hello-world is more priority than atoum
                ]
            ))
            ->and($this->mock->addModule('hello-world'))
            ->and($this->mock->addModule('atoum'))
            ->variable($this->mock->generateTree())
                ->isNull()
            ->array($tree = $this->mock->getLoadTree())
                ->isNotEmpty()
        ;
    }
}
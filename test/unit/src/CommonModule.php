<?php

namespace BFW\test\unit;

use atoum;

require_once(__DIR__ . '/../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class CommonModule extends atoum
{
    use \BFW\Test\Helpers\Application;

    protected $mock;

    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__ . '/../../..');
        $this->createApp();
        $this->initApp();

        // Create a concrete implementation for testing
        eval('
            namespace BFW\\test\\unit;
            
            class ConcreteModule extends \\BFW\\CommonModule
            {
                public function run(): void
                {
                    // Concrete implementation for testing
                }
            }
        ');
    }

    public function testCommonModuleImplementsInterface()
    {
        $this->assert('test CommonModule implements ModuleInterface')
            ->class('BFW\CommonModule')
                ->hasInterface('BFW\ModuleInterface')
        ;
    }

    public function testCommonModuleIsAbstract()
    {
        $this->assert('test CommonModule is abstract')
            ->class('BFW\CommonModule')
                ->isAbstract()
        ;
    }

    public function testModuleNameGetterSetter()
    {
        $module = new \BFW\Module('test');
        $this->mock = new \BFW\test\unit\ConcreteModule($module);

        $this->assert('test CommonModule module name getter/setter')
            ->variable($this->mock->getModuleName())
                ->isNull()
            ->object($this->mock->setModuleName('test-module'))
                ->isIdenticalTo($this->mock)
            ->string($this->mock->getModuleName())
                ->isEqualTo('test-module')
        ;
    }

    public function testConfigGetterSetter()
    {
        $module = new \BFW\Module('test');
        $this->mock = new \BFW\test\unit\ConcreteModule($module);

        $config = new \BFW\Config('test');

        $this->assert('test CommonModule config getter/setter')
            ->variable($this->mock->getConfig())
                ->isNull()
            ->object($this->mock->setConfig($config))
                ->isIdenticalTo($this->mock)
            ->object($this->mock->getConfig())
                ->isIdenticalTo($config)
        ;
    }

    public function testModuleGetterConstructor()
    {
        $module = new \BFW\Module('test');
        $this->mock = new \BFW\test\unit\ConcreteModule($module);

        $this->assert('test CommonModule stores module instance from constructor')
            ->object($this->mock->getModule())
                ->isIdenticalTo($module)
        ;
    }

    public function testRunMethodExists()
    {
        $this->assert('test CommonModule has abstract run method')
            ->class('BFW\CommonModule')
                ->hasMethod('run')
        ;
    }
}
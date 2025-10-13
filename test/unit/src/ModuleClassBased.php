<?php

namespace BFW\test\unit;

use atoum;

require_once(__DIR__ . '/../../../vendor/autoload.php');

/**
 * Test class for class-based module functionality
 * 
 * @engine isolate
 */
class ModuleClassBased extends atoum
{
    use \BFW\Test\Helpers\Application;

    protected $mock;

    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__ . '/../../..');
        $this->createApp();
        $this->initApp();

        $this->mockGenerator
            ->makeVisible('obtainRunnerClass')
            ->makeVisible('instantiateModuleClass')
            ->generate('BFW\Module')
        ;

        if ($testMethod === 'testConstruct') {
            return;
        }

        $this->mock = new \mock\BFW\Module('atoum');
    }

    protected function mockLoadJsonFile($filePath, $json)
    {
        $this->given($this->function->file_exists = function ($path) use (&$filePath) {
                return ($path === $filePath);
        })
            ->given($this->function->file_get_contents = function ($path) use (&$filePath, $json) {
                if ($path !== $filePath) {
                    return false;
                }

                return $json;
            })
        ;
    }

    public function testObtainRunnerClass()
    {
        $this->assert('test Module::obtainRunnerClass without class property')
            ->variable($this->invoke($this->mock)->obtainRunnerClass())
                ->isNull()
        ;

        $this->assert('test Module::obtainRunnerClass with class property')
            ->given($this->mock->setLoadInfos((object) [
                'class' => 'MyModule\\TestModule'
            ]))
            ->string($this->invoke($this->mock)->obtainRunnerClass())
                ->isEqualTo('MyModule\\TestModule')
        ;
    }

    public function testRunModuleWithClassBasedRunner()
    {
        // Create a test module class that implements ModuleInterface
        eval('
            namespace BFW\\test\\unit;
            
            class TestModuleRunner extends \\BFW\\CommonModule
            {
                public $runCalled = false;
                
                public function run(): void
                {
                    $this->runCalled = true;
                }
            }
        ');

        $this->assert('test Module::runModule with class-based runner')
            ->given($this->mock->setLoadInfos((object) [
                'class' => 'BFW\\test\\unit\\TestModuleRunner'
            ]))
            ->and($this->function->class_exists = function ($className) {
                return $className === 'BFW\\test\\unit\\TestModuleRunner';
            })
            ->variable($this->mock->runModule())
                ->isNull()
            ->boolean($this->mock->isRun())
                ->isTrue()
        ;
    }

    public function testInstantiateModuleClassNotFound()
    {
        $this->assert('test Module::instantiateModuleClass with non-existent class')
            ->given($this->mock->setLoadInfos((object) [
                'class' => 'NonExistent\\Module'
            ]))
            ->and($this->function->class_exists = false)
            ->exception(function () {
                $this->invoke($this->mock)->instantiateModuleClass();
            })
                ->hasCode(\BFW\Module::ERR_CLASS_NOT_FOUND)
        ;
    }

    public function testInstantiateModuleClassWrongInterface()
    {
        // Create a class that doesn't implement ModuleInterface
        eval('
            namespace BFW\\test\\unit;
            
            class BadTestModule
            {
                public function run(): void
                {
                    // Does not implement ModuleInterface
                }
            }
        ');

        $this->assert('test Module::instantiateModuleClass with class not implementing ModuleInterface')
            ->given($this->mock->setLoadInfos((object) [
                'class' => 'BFW\\test\\unit\\BadTestModule'
            ]))
            ->and($this->function->class_exists = function ($className) {
                return $className === 'BFW\\test\\unit\\BadTestModule';
            })
            ->exception(function () {
                $this->invoke($this->mock)->instantiateModuleClass();
            })
                ->hasCode(\BFW\Module::ERR_METHOD_NOT_EXIST)
        ;
    }
}
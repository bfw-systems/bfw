<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class RunTasks extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    protected $observer;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../..');
        $this->createApp();
        $this->initApp();
        
        if ($testMethod === 'testConstruct') {
            return;
        }
        
        $this->observer = new \BFW\Test\Helpers\ObserverArray;
        $this->mock     = new \mock\BFW\RunTasks([], 'unitTest');
        $this->mock->attach($this->observer);
    }
    
    public function testConstruct()
    {
        $this->assert('test Constructor')
            ->object($runTasks = new \mock\BFW\RunTasks([], 'unitTest'))
                ->isInstanceOf('\BFW\RunTasks')
            ->array($runTasks->getRunSteps())
                ->isEmpty()
            ->string($runTasks->getNotifyPrefix())
                ->isEqualto('unitTest')
        ;
    }
    
    public function testGetSetAddRunSteps()
    {
        $this->assert('test RunTasks::getRunSteps with construct value')
            ->array($this->mock->getRunSteps())
                ->isEmpty()
        ;
        
        $this->assert('test RunTasks::setRunSteps')
            ->given($objAtoumStep = \BFW\RunTasks::generateStepItem($this))
            ->object($this->mock->setRunSteps([
                'atoum' => $objAtoumStep
            ]))
                ->isIdenticalTo($this->mock)
            ->array($this->mock->getRunSteps())
                ->isEqualTo([
                    'atoum' => $objAtoumStep
                ])
        ;
        
        $this->assert('test RunTasks::addRunSteps')
            ->given($objHelloStep = \BFW\RunTasks::generateStepItem(
                null,
                function() {
                    echo 'hello world !';
                }
            ))
            ->object($this->mock->addToRunSteps('hello', $objHelloStep))
                ->isIdenticalTo($this->mock)
            ->array($this->mock->getRunSteps())
                ->isEqualTo([
                    'atoum' => $objAtoumStep,
                    'hello' => $objHelloStep
                ])
        ;
    }
    
    public function testGetAndSetNotifyPrefix()
    {
        $this->assert('test RunTasks::getNotifyPrefix with construct value')
            ->string($this->mock->getNotifyPrefix())
                ->isEqualTo('unitTest')
        ;
        
        $this->assert('test RunTasks::setNotifyPrefix')
            ->object($this->mock->setNotifyPrefix('atoum'))
                ->isIdenticalTo($this->mock)
            ->string($this->mock->getNotifyPrefix())
                ->isEqualTo('atoum')
        ;
    }
    
    public function testRun()
    {
        $this->assert('test RunTasks::run - prepare')
            ->given($helloOutput = '')
            ->given($this->mock->setRunSteps([
                'atoum' => \BFW\RunTasks::generateStepItem($this),
                'hello' => \BFW\RunTasks::generateStepItem(
                    null,
                    function() use(&$helloOutput) {
                        $helloOutput = 'hello world !';
                    }
                )
            ]))
        ;
        
        $this->assert('test RunTasks::run')
            ->variable($this->mock->run())
            ->string($helloOutput)
                ->isEqualTo('hello world !')
            ->array($received = $this->observer->getUpdateReceived())
                ->hasSize(5)
                ->object($received[0])
                    ->string($received[0]->action)
                        ->isEqualTo('unitTest_start_run_tasks')
                    ->variable($received[0]->context)
                        ->isNull()
                ->object($received[1])
                    ->string($received[1]->action)
                        ->isEqualTo('unitTest_exec_atoum')
                    ->object($received[1]->context)
                        ->isIdenticalTo($this)
                ->object($received[2])
                    ->string($received[2]->action)
                        ->isEqualTo('unitTest_run_hello')
                    ->variable($received[2]->context)
                        ->isNull()
                ->object($received[3])
                    ->string($received[3]->action)
                        ->isEqualTo('unitTest_done_hello')
                    ->variable($received[3]->context)
                        ->isNull()
                ->object($received[4])
                    ->string($received[4]->action)
                        ->isEqualTo('unitTest_end_run_tasks')
                    ->variable($received[4]->context)
                        ->isNull()
        ;
    }
    
    public function testSendNotify()
    {
        $this->assert('test RunTasks::sendNotify without context')
            ->variable($this->mock->sendNotify('hello_from_unit_test'))
                ->isNull()
            ->array($received = $this->observer->getUpdateReceived())
                ->hasSize(1)
                ->object($received[0])
                    ->string($received[0]->action)
                        ->isEqualTo('hello_from_unit_test')
                    ->variable($received[0]->context)
                        ->isNull()
        ;
        
        $this->assert('test RunTasks::sendNotify with context')
            ->variable($this->mock->sendNotify('hi_from_atoum', $this))
                ->isNull()
            ->array($received = $this->observer->getUpdateReceived())
                ->hasSize(2)
                ->object($received[0])
                    ->string($received[0]->action)
                        ->isEqualTo('hello_from_unit_test')
                    ->variable($received[0]->context)
                        ->isNull()
                ->object($received[1])
                    ->string($received[1]->action)
                        ->isEqualTo('hi_from_atoum')
                    ->object($received[1]->context)
                        ->isIdenticalTo($this)
        ;
    }
}
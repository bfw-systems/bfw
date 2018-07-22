<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

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
        $this->setRootDir(__DIR__.'/../../../..');
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
            ->given($objAtoumStep = (object) [
                'context' => $this
            ])
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
            ->given($objHelloStep = (object) [
                'callback' => function() {
                    echo 'hello world !';
                }
            ])
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
                'atoum' => (object) [
                    'context' => $this
                ],
                'hello' => (object) [
                    'callback' => function() use(&$helloOutput) {
                        $helloOutput = 'hello world !';
                    }
                ]
            ]))
        ;
        
        $this->assert('test RunTasks::run')
            ->variable($this->mock->run())
            ->string($helloOutput)
                ->isEqualTo('hello world !')
            ->array($this->observer->getUpdateReceived())
                ->isEqualTo([
                    (object) [
                        'action'  => 'unitTest_start_run_tasks',
                        'context' => null
                    ],
                    (object) [
                        'action'  => 'unitTest_exec_atoum',
                        'context' => $this
                    ],
                    (object) [
                        'action'  => 'unitTest_run_hello',
                        'context' => null
                    ],
                    (object) [
                        'action'  => 'unitTest_done_hello',
                        'context' => null
                    ],
                    (object) [
                        'action'  => 'unitTest_end_run_tasks',
                        'context' => null
                    ]
                ])
        ;
    }
    
    public function testSendNotify()
    {
        $this->assert('test RunTasks::sendNotify without context')
            ->variable($this->mock->sendNotify('hello_from_unit_test'))
                ->isNull()
            ->array($this->observer->getUpdateReceived())
                ->isEqualTo([
                    (object) [
                        'action'  => 'hello_from_unit_test',
                        'context' => null
                    ]
                ])
        ;
        
        $this->assert('test RunTasks::sendNotify with context')
            ->variable($this->mock->sendNotify('hi_from_atoum', $this))
                ->isNull()
            ->array($this->observer->getUpdateReceived())
                ->isEqualTo([
                    (object) [
                        'action'  => 'hello_from_unit_test',
                        'context' => null
                    ],
                    (object) [
                        'action'  => 'hi_from_atoum',
                        'context' => $this
                    ]
                ])
        ;
    }
}
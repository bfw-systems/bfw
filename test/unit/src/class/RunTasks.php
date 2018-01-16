<?php

namespace BFW\test\unit;

use \atoum;
use \BFW\test\unit\mocks\Observer as MockObserver;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class RunTasks extends atoum
{
    protected $class;
    
    /**
     * Call before each test method
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        $this->class = new \BFW\RunTasks([], 'unitTest');
        $this->class->attach(new MockObserver);
    }
    
    /**
     * Test the constructor
     */
    public function testConstructor()
    {
        $this->assert('test Constructor')
            ->object($this->class)
                ->isInstanceOf('\BFW\RunTasks')
                ->isInstanceOf('\BFW\Subjects')
            ->array($this->class->getRunSteps())
                ->isEmpty()
            ->string($this->class->getNotifyPrefix())
                ->isEqualTo('unitTest')
        ;
    }
    
    public function testSetters()
    {
        $this->assert('test setter for runSteps')
            ->array($this->class->getRunSteps())
                ->isEmpty()
            ->given($runSteps = [
                'testSetter' => (object) []
            ])
            ->object($this->class->setRunSteps($runSteps))
                ->isIdenticalTo($this->class)
            ->array($this->class->getRunSteps())
                ->isEqualTo($runSteps)
        ;
        
        $this->assert('test setter for notifyPrefix')
            ->string($this->class->getNotifyPrefix())
                ->isEqualTo('unitTest')
            ->object($this->class->setNotifyPrefix('unitTestSetters'))
                ->isIdenticalTo($this->class)
            ->string($this->class->getNotifyPrefix())
                ->isEqualTo('unitTestSetters')
        ;
    }
    
    public function testAddToRunSteps()
    {
        $this->assert('test method addToRunSteps')
            ->array($this->class->getRunSteps())
                ->isEmpty()
            ->given($runStep1 = (object) [])
            ->object($this->class->addToRunSteps('test1', $runStep1))
                ->isIdenticalTo($this->class)
            ->array($this->class->getRunSteps())
                ->isEqualTo([
                    'test1' => $runStep1
                ])
            ->given($runStep2 = (object) [])
            ->object($this->class->addToRunSteps('test2', $runStep2))
                ->isIdenticalTo($this->class)
            ->array($this->class->getRunSteps())
                ->isEqualTo([
                    'test1' => $runStep1,
                    'test2' => $runStep2
                ])
        ;
    }
    
    public function testRun()
    {
        $this->assert('test method run without steps')
            ->array($this->class->getRunSteps())
                ->isEmpty()
            ->output(function() {
                $this->class->run();
            })
                ->isEqualTo(
                    'unitTest_start_run_tasks'."\n"
                    .'unitTest_end_run_tasks'."\n"
                )
        ;
        
        $this->assert('test method run with one step, no callback or context')
            ->if($this->class->addToRunSteps('test1', (object) []))
            ->then
            ->output(function() {
                $this->class->run();
            })
                ->isEqualTo(
                    'unitTest_start_run_tasks'."\n"
                    .'unitTest_exec_test1'."\n"
                    .'unitTest_end_run_tasks'."\n"
                )
        ;
        
        $this->assert('test method run with one step, with callback but no context')
            ->if($this->class->addToRunSteps('test1', (object) [
                'callback' => function() {
                    echo 'echo from callback :)'."\n";
                }
            ]))
            ->then
            ->output(function() {
                $this->class->run();
            })
                ->isEqualTo(
                    'unitTest_start_run_tasks'."\n"
                    .'unitTest_run_test1'."\n"
                    .'echo from callback :)'."\n"
                    .'unitTest_finish_test1'."\n"
                    .'unitTest_end_run_tasks'."\n"
                )
        ;
    }
    
    public function testSendNotify()
    {
        $this->assert('test method sendNotify')
            ->output(function() {
                $this->class->sendNotify('notify from unit test');
            })
                ->isEqualTo('notify from unit test'."\n")
        ;
    }
}

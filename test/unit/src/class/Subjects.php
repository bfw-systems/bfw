<?php

namespace BFW\test\unit;

use \atoum;
use \BFW\test\unit\mocks\Observer as MockObserver;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Subjects extends atoum
{
    /**
     * @var $class Class instance
     */
    protected $class;

    /**
     * Call before each test method
     * Instantiate the class
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        $this->class = new \BFW\Subjects();
    }
    
    /**
     * Test method for attach() and getObservers()
     * 
     * @return void
     */
    public function testAttachAndGetObservers()
    {
        $this->assert('test Subjects attach and getObservers')
            ->array($this->class->getObservers())
                ->hasSize(0)
            ->given($observer = new MockObserver)
            ->given($this->class->attach($observer))
            ->array($getObservers = $this->class->getObservers())
                ->hasSize(1)
            ->object($getObservers[0])
                ->isInstanceOf('\BFW\test\unit\mocks\Observer')
                ->isEqualTo($observer);
    }
    
    /**
     * Test method for detach()
     * 
     * @return void
     */
    public function testDetach()
    {
        $this->assert('test Subjects detach')
            ->given($observer = new MockObserver)
            ->given($this->class->attach($observer))
            ->array($getObservers = $this->class->getObservers())
                ->hasSize(1)
            ->given($this->class->detach($observer))
            ->array($this->class->getObservers())
                ->hasSize(0);
    }
    
    /**
     * Test method for getAction() and setAction()
     * 
     * @return void
     */
    public function testGetAndSetAction()
    {
        $this->assert('test Subjects getAction and setAction')
            ->string($this->class->getAction())
                ->isEmpty()
            ->given($this->class->setAction('unit_test'))
            ->string($this->class->getAction())
                ->isEqualTo('unit_test');
    }
    
    /**
     * Test method for getContext() and setContext()
     * 
     * @return void
     */
    public function testGetAndSetContext()
    {
        $this->assert('test Subjects getContext and setContext')
            ->variable($this->class->getContext())
                ->isNull()
            ->given($this->class->setContext([
                'test' => 'unit',
                'lib' => 'atoum'
            ]))
            ->array($this->class->getContext())
                ->isEqualTo([
                'test' => 'unit',
                'lib' => 'atoum'
            ]);
    }
    
    /**
     * Test method for notify()
     * 
     * @return void
     */
    public function testNotify()
    {
        $this->assert('test notify')
            ->given($observer = new MockObserver)
            ->given($this->class->attach($observer))
            ->given($class = $this->class)
            ->output(function() use ($class) {
                $class->notify();
            })->isEqualTo("\n")
            ->if($this->class->setAction('unit_test'))
            ->then
            ->output(function() use ($class) {
                $class->notify();
            })->isEqualTo('unit_test'."\n");
    }
    
    /**
     * Test method for notifyAction()
     * 
     * @return void
     */
    public function testNotifyAction()
    {
        $this->assert('test notifyAction')
            ->given($observer = new MockObserver)
            ->given($this->class->attach($observer))
            ->given($class = $this->class)
            ->output(function() use ($class) {
                $class->notifyAction('unit_test');
            })->isEqualTo('unit_test'."\n");
    }
}

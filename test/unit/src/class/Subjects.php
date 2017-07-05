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
        $this->class = new \BFW\Subjects;
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
     * Test method for getAction()
     * 
     * @return void
     */
    public function testGetAction()
    {
        $this->assert('test Subjects getAction')
            ->string($this->class->getAction())
                ->isEmpty()
            ->if($this->class->addNotification('unit_test'))
            ->then
            ->string($this->class->getAction())
                ->isEqualTo('unit_test');
    }
    
    /**
     * Test method for getContext()
     * 
     * @return void
     */
    public function testGetContext()
    {
        $this->assert('test Subjects getContext and setContext')
            ->variable($this->class->getContext())
                ->isNull()
            ->if($this->class->addNotification(
                'unit_test',
                [
                    'test' => 'unit',
                    'lib' => 'atoum'
                ]
            ))
            ->then
            ->string($this->class->getAction())
                ->isEqualTo('unit_test')
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
            })->isEqualTo("\n");
    }
    
    /**
     * Test method for addNotification()
     * 
     * @return void
     */
    public function testAddNotification()
    {
        $observer = [
            new MockObserver,
            new \mock\BFW\test\unit\mocks\Observer //Atoum mock
        ];
        
        //Modify the method update to the second observer to test the case
        //Where update send a new notification.
        $this->calling($observer[1])->update = function(\SplSubject $subject) {
            if ($subject->getAction() === 'unit_test') {
                $subject->addNotification('unit_test2');
            }
            
            echo $subject->getAction()."\n";
        };
        
        $this->assert('test addNotification with one observer')
            ->given($this->class->attach($observer[0]))
            ->output(function() {
                $this->class->addNotification('unit_test');
            })->isEqualTo('unit_test'."\n");
            
        $this->assert(
            'test addNotification with two observer. '
            .'Re-send notification into the second.'
        )
            ->given($this->class->attach($observer[1]))
            ->output(function() {
                $this->class->addNotification('unit_test');
            })->isEqualTo(
                'unit_test'."\n"
                .'unit_test'."\n"
                .'unit_test2'."\n"
                .'unit_test2'."\n"
            );
            
        $this->assert(
            'test addNotification with two observer. '
            .'Re-send notification into the first.'
        )
            ->given($this->class->detach($observer[0]))
            ->given($this->class->detach($observer[1]))
            ->given($this->class->attach($observer[1]))
            ->given($this->class->attach($observer[0]))
            ->output(function() {
                $this->class->addNotification('unit_test');
            })->isEqualTo(
                'unit_test'."\n"
                .'unit_test'."\n"
                .'unit_test2'."\n"
                .'unit_test2'."\n"
            );
    }
}

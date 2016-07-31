<?php

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Subjects extends atoum
{
    /**
     * @var $class : Instance de la class
     */
    protected $class;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        $this->class = new \BFW\Subjects();
    }
    
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
                ->isInstanceOf('BFW\test\unit\MockObserver')
                ->isEqualTo($observer);
    }
    
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
    
    public function testGetAndSetAction()
    {
        $this->assert('test Subjects getAction and setAction')
            ->string($this->class->getAction())
                ->isEmpty()
            ->given($this->class->setAction('unit_test'))
            ->string($this->class->getAction())
                ->isEqualTo('unit_test');
    }
    
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
    
    public function testNotify()
    {
        $this->assert('test notify')
            ->given($observer = new MockObserver)
            ->given($this->class->attach($observer))
            ->given($class = $this->class)
            ->output(function() use ($class) {
                $class->notify();
            })->isEqualTo('')
            ->if($this->class->setAction('unit_test'))
            ->then
            ->output(function() use ($class) {
                $class->notify();
            })->isEqualTo('unit_test');
    }
    
    public function testNotifyAction()
    {
        $this->assert('test notifyAction')
            ->given($observer = new MockObserver)
            ->given($this->class->attach($observer))
            ->given($class = $this->class)
            ->output(function() use ($class) {
                $class->notifyAction('unit_test');
            })->isEqualTo('unit_test');
    }
}

class MockObserver implements \SplObserver
{
    public function update(\SplSubject $subject)
    {
        echo $subject->getAction();
    }
}
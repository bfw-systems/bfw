<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Subject extends atoum
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
        $this->mock     = new \BFW\Test\Mock\Subject;
        
        if (
            $testMethod === 'testGettersDefaultValues' ||
            $testMethod === 'testAttachAndDetach'
        ) {
            return;
        }
        
        $this->mock->attach($this->observer);
    }
    
    protected function newNotify($action, $context)
    {
        return new class($action, $context) {
            public $action;
            public $context;
            
            public function __construct($action, $context) {
                $this->action  = $action;
                $this->context = $context;
            }
        };
    }
    
    public function testConstruct()
    {
        $this->assert('test Constructor')
            ->object($runTasks = new \mock\BFW\Subject)
                ->isInstanceOf('\BFW\Subject')
                ->IsInstanceOf('\SplSubject')
        ;
    }
    
    public function testGettersDefaultValues()
    {
        $this->assert('test Subject::getObservers for default value')
            ->array($this->mock->getObservers())
                ->isEmpty()
        ;
        
        $this->assert('test Subject::getNotifyHeap for default value')
            ->array($this->mock->getNotifyHeap())
                ->isEmpty()
        ;
        
        $this->assert('test Subject::getAction for default value')
            ->string($this->mock->getAction())
                ->isEmpty()
        ;
        
        $this->assert('test Subject::getContext for default value')
            ->variable($this->mock->getContext())
                ->isNull()
        ;
    }
    
    public function testAttachAndDetach()
    {
        $this->assert('test Subject::attach')
            ->object($this->mock->attach($this->observer))
                ->isIdenticalTo($this->mock)
            ->array($observerList = $this->mock->getObservers())
                ->size
                    ->isEqualTo(1)
            ->object($observerList[0])
                ->isIdenticalTo($this->observer)
        ;
        
        $this->assert('test Subject::detach')
            ->object($this->mock->detach($this->observer))
                ->isIdenticalTo($this->mock)
            ->array($observerList = $this->mock->getObservers())
                ->isEmpty()
            ->exception(function() {
                $this->mock->detach($this->observer);
            })
                ->hasCode(\BFW\Subject::ERR_OBSERVER_NOT_FOUND)
        ;
    }
    
    public function testNotify()
    {
        $this->assert('test Subject::notify')
            ->object($this->mock->notify())
                ->isIdenticalTo($this->mock)
            ->array($this->observer->getUpdateReceived())
                ->size
                    ->isEqualTo(1)
        ;
    }
    
    public function testReadNotifyHeap()
    {
        $this->mock = new \mock\BFW\Test\Mock\Subject;
        
        $this->assert('test Subject::readNotifyHeap')
            ->given($notifyList = [])
            ->given($mock = $this->mock)
            ->given($atoum = $this)
            ->if($this->calling($this->mock)->notify = function() use (&$notifyList, &$mock, $atoum) {
                $notifyList[] = $atoum->newNotify(
                    $mock->getAction(),
                    $mock->getContext()
                );
                
                if ($mock->getAction() === 'add_new_notify') {
                    $mock->addNotifyHeap('hello', 'world !');
                }
            })
            ->and($this->mock->setNotifyHeap([
                $this->newNotify('atoum', $this),
                $this->newNotify('add_new_notify', null),
                $this->newNotify('hi', null)
            ]))
            ->then
            
            ->object($this->mock->readNotifyHeap())
                ->isIdenticalTo($this->mock)
            ->array($notifyList)
                ->hasSize(4)
                ->object($notifyList[0])
                    ->string($notifyList[0]->action)
                        ->isEqualTo('atoum')
                    ->object($notifyList[0]->context)
                        ->isIdenticalTo($this)
                ->object($notifyList[1])
                    ->string($notifyList[1]->action)
                        ->isEqualTo('add_new_notify')
                    ->variable($notifyList[1]->context)
                        ->isNull()
                ->object($notifyList[2])
                    ->string($notifyList[2]->action)
                        ->isEqualTo('hi')
                    ->variable($notifyList[2]->context)
                        ->isNull()
                ->object($notifyList[3])
                    ->string($notifyList[3]->action)
                        ->isEqualTo('hello')
                    ->string($notifyList[3]->context)
                        ->isEqualTo('world !')
        ;
    }
    
    public function testAddNotification()
    {
        $this->mock = new \mock\BFW\Test\Mock\Subject;
        
        $this->assert('test Subject::addNotification for first call')
            ->given($nbCallToReadNotifyHeap = 0)
            ->if($this->calling($this->mock)->readNotifyHeap = function() use (&$nbCallToReadNotifyHeap) {
                $nbCallToReadNotifyHeap++;
            })
            ->then
            ->object($this->mock->addNotification('atoum'))
                ->isIdenticalTo($this->mock)
            ->integer($nbCallToReadNotifyHeap)
                ->isEqualTo(1)
            ->array($notifyHeap = $this->mock->getNotifyHeap())
                ->hasSize(1)
                ->object($notifyHeap[0])
                    ->string($notifyHeap[0]->action)
                        ->isEqualTo('atoum')
                    ->variable($notifyHeap[0]->context)
                        ->isNull()
        ;

        $this->assert('test Subject::addNotification for second call')
            ->object($this->mock->addNotification('hello', 'world !'))
                ->isIdenticalTo($this->mock)
            ->integer($nbCallToReadNotifyHeap)
                ->isEqualTo(1) //Not recall
            ->array($notifyHeap = $this->mock->getNotifyHeap())
                ->hasSize(2)
                ->object($notifyHeap[0])
                    ->string($notifyHeap[0]->action)
                        ->isEqualTo('atoum')
                    ->variable($notifyHeap[0]->context)
                        ->isNull()
                ->object($notifyHeap[1])
                    ->string($notifyHeap[1]->action)
                        ->isEqualTo('hello')
                    ->string($notifyHeap[1]->context)
                        ->isEqualTo('world !')
        ;
    }
}
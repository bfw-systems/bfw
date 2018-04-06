<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Subjects extends atoum
{
    //use \BFW\Test\Helpers\Application;
    
    protected $mock;
    protected $observer;
    
    public function beforeTestMethod($testMethod)
    {
        //$this->createApp();
        //$this->initApp();
        
        if ($testMethod === 'testConstruct') {
            return;
        }
        
        $this->observer = new \BFW\Test\Helpers\ObserverArray;
        $this->mock     = new \BFW\Test\Mock\Subjects;
        
        if (
            $testMethod === 'testGettersDefaultValues' ||
            $testMethod === 'testAttachAndDetach'
        ) {
            return;
        }
        
        $this->mock->attach($this->observer);
    }
    
    public function testConstruct()
    {
        $this->assert('test Constructor')
            ->object($runTasks = new \mock\BFW\Subjects)
                ->isInstanceOf('\BFW\Subjects')
                ->IsInstanceOf('\SplSubject')
        ;
    }
    
    public function testGettersDefaultValues()
    {
        $this->assert('test Subjects::getObservers for default value')
            ->array($this->mock->getObservers())
                ->isEmpty()
        ;
        
        $this->assert('test Subjects::getNotifyHeap for default value')
            ->array($this->mock->getNotifyHeap())
                ->isEmpty()
        ;
        
        $this->assert('test Subjects::getAction for default value')
            ->string($this->mock->getAction())
                ->isEmpty()
        ;
        
        $this->assert('test Subjects::getContext for default value')
            ->variable($this->mock->getContext())
                ->isNull()
        ;
    }
    
    public function testAttachAndDetach()
    {
        $this->assert('test Subjects::attach')
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
                ->hasCode(\BFW\Subjects::ERR_OBSERVER_NOT_FOUND)
        ;
    }
    
    public function testNotify()
    {
        $this->assert('test Subjects::notify')
            ->object($this->mock->notify())
                ->isIdenticalTo($this->mock)
            ->array($this->observer->getUpdateReceived())
                ->size
                    ->isEqualTo(1)
        ;
    }
    
    public function testReadNotifyHeap()
    {
        $this->mock = new \mock\BFW\Test\Mock\Subjects;
        
        $this->assert('test Subjects::readNotifyHeap')
            ->given($notifyList = [])
            ->given($mock = $this->mock)
            ->if($this->calling($this->mock)->notify = function() use (&$notifyList, &$mock) {
                $notifyList[] = (object) [
                    'action'  => $mock->getAction(),
                    'context' => $mock->getContext()
                ];
                
                if ($mock->getAction() === 'add_new_notify') {
                    $mock->addNotifyHeap('hello', 'world !');
                }
            })
            ->and($this->mock->setNotifyHeap([
                (object) [
                    'action'  => 'atoum',
                    'context' => $this
                ],
                (object) [
                    'action'  => 'add_new_notify',
                    'context' => null
                ],
                (object) [
                    'action'  => 'hi',
                    'context' => null
                ]
            ]))
            ->then
            
            ->object($this->mock->readNotifyHeap())
                ->isIdenticalTo($this->mock)
            ->array($notifyList)
                ->size
                    ->isEqualTo(4)
            ->object($notifyList[0])
                ->isEqualTo((object) [
                    'action'  => 'atoum',
                    'context' => $this
                ])
            ->object($notifyList[1])
                ->isEqualTo((object) [
                    'action'  => 'add_new_notify',
                    'context' => null
                ])
            ->object($notifyList[2])
                ->isEqualTo((object) [
                    'action'  => 'hi',
                    'context' => null
                ])
            ->object($notifyList[3])
                ->isEqualTo((object) [
                    'action'  => 'hello',
                    'context' => 'world !'
                ])
        ;
    }
    
    public function testAddNotification()
    {
        $this->mock = new \mock\BFW\Test\Mock\Subjects;
        
        $this->assert('test Subjects::addNotification for first call')
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
                ->size
                    ->isEqualTo(1)
            ->object($notifyHeap[0])
                ->isEqualTo((object) [
                    'action'  => 'atoum',
                    'context' => null
                ])
        ;

        $this->assert('test Subjects::addNotification for second call')
            ->object($this->mock->addNotification('hello', 'world !'))
                ->isIdenticalTo($this->mock)
            ->integer($nbCallToReadNotifyHeap)
                ->isEqualTo(1) //Not recall
            ->array($notifyHeap = $this->mock->getNotifyHeap())
                ->size
                    ->isEqualTo(2)
            ->object($notifyHeap[0])
                ->isEqualTo((object) [
                    'action'  => 'atoum',
                    'context' => null
                ])
            ->object($notifyHeap[1])
                ->isEqualTo((object) [
                    'action'  => 'hello',
                    'context' => 'world !'
                ])
        ;
    }
}
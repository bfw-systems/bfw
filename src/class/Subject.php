<?php

namespace BFW;

use \Exception;
use \SplSubject;
use \SplObserver;

/**
 * Class to manage subject in observers systems
 */
class Subject implements SplSubject
{
    /**
     * @const ERR_OBSERVER_NOT_FOUND Exception code if the observer to detach
     * has not been found.
     */
    const ERR_OBSERVER_NOT_FOUND = 1112001;
    
    /**
     * @var \SplObserver[] $observers List of all observers
     */
    protected $observers = [];
    
    /**
     * @var \stdClass[] $notifyHeap List of notify to send
     */
    protected $notifyHeap = [];
    
    /**
     * @var string $action The current action to send to observers
     */
    protected $action = '';
    
    /**
     * @var mixed $context The current context to send to observers
     */
    protected $context = null;
    
    /**
     * Return list of all observers
     * 
     * @return \SplObserver[]
     */
    public function getObservers()
    {
        return $this->observers;
    }
    
    /**
     * Return list of all notify to send
     * 
     * @return \stdClass[]
     */
    public function getNotifyHeap()
    {
        return $this->notifyHeap;
    }
    
    /**
     * Return the action
     * 
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Return the context
     * 
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Attach a new observer to the list
     * 
     * @param \SplObserver $observer The new observer
     * 
     * @return \BFW\Subject The current instance of this class
     */
    public function attach(SplObserver $observer)
    {
        $this->observers[] = $observer;

        return $this;
    }

    /**
     * Detach a observer to the list
     * 
     * @param \SplObserver $observer The observer instance to detach
     * 
     * @return \BFW\Subject The current instance of this class
     */
    public function detach(SplObserver $observer)
    {
        $key = array_search($observer, $this->observers, true);
        
        if ($key === false) {
            throw new Exception(
                'The observer has not been found.',
                self::ERR_OBSERVER_NOT_FOUND
            );
        }
        
        unset($this->observers[$key]);

        return $this;
    }

    /**
     * Send a notification to all observers
     * 
     * @return \BFW\Subject The current instance of this class
     */
    public function notify()
    {
        \BFW\Application::getInstance()
            ->getMonolog()
            ->getLogger()
            ->debug(
                'Subject notify event',
                ['action' => $this->action]
            );
        
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
        
        return $this;
    }
    
    /**
     * Read the notify heap list and send each notify into the list.
     * 
     * @return $this
     */
    public function readNotifyHeap()
    {
        foreach ($this->notifyHeap as $notifyIndex => $notifyDatas) {
            $this->action  = $notifyDatas->action;
            $this->context = $notifyDatas->context;
            
            $this->notify();
            
            //Remove the current notification from list
            unset($this->notifyHeap[$notifyIndex]);
        }
        
        //Some new notifications has been added during the loop
        if (count($this->notifyHeap) > 0) {
            $this->readNotifyHeap();
        }

        return $this;
    }
    
    /**
     * Add a new notification to the list of notification to send.
     * If there is only one notification into the list, it will be send now.
     * Else, a notification is currently sent, so we wait it finish and the
     * current notification will be sent.
     * 
     * @param string $action The action to send
     * @param notification $context (default null) The context to send
     * 
     * @return \BFW\Subject The current instance of this class
     */
    public function addNotification($action, $context = null)
    {
        $this->notifyHeap[] = new class($action, $context) {
            public $action;
            public $context;
            
            public function __construct($action, $context) {
                $this->action  = $action;
                $this->context = $context;
            }
        };
        
        if (count($this->notifyHeap) === 1) {
            $this->readNotifyHeap();
        }
        
        return $this;
    }
}

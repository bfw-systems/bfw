<?php

namespace BFW\Test\Mock;

/**
 * Mock for Subject class
 */
class Subject extends \BFW\Subject
{
    /**
     * Setter to property observers
     * 
     * @param array $observers
     * 
     * @return $this
     */
    public function setObservers(array $observers)
    {
        $this->observers = $observers;
        return $this;
    }

    /**
     * Setter to property notifyHeap
     * 
     * @param array $notifyHeap
     * 
     * @return $this
     */
    public function setNotifyHeap(array $notifyHeap)
    {
        $this->notifyHeap = $notifyHeap;
        return $this;
    }

    /**
     * Setter to property action
     * 
     * @param string $action
     * 
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Setter to property context
     * 
     * @param mixed $context
     * 
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }
    
    /**
     * Add a new item into the notifyHeap list
     * 
     * @param string $action
     * @param mixed $context
     * 
     * @return void
     */
    public function addNotifyHeap($action, $context)
    {
        $this->notifyHeap[] = new class($action, $context) {
            public $action;
            public $context;
            
            public function __construct($action, $context) {
                $this->action  = $action;
                $this->context = $context;
            }
        };
    }
}

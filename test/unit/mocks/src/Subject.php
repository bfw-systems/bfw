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
    public function setObservers(array $observers): self
    {
        $this->observers = $observers;
        return $this;
    }

    /**
     * Setter to property notifyHeap
     * For backward compatibility with tests
     * 
     * @param array $notifyHeap
     * 
     * @return $this
     */
    public function setNotifyHeap(array $notifyHeap): self
    {
        // Clear current queue and add new events
        $currentQueue = $this->getEventDispatcher()->getEventQueue();
        while (!empty($currentQueue)) {
            array_shift($currentQueue);
        }
        
        foreach ($notifyHeap as $item) {
            $event = new \BFW\Events\Event($item->action, $item->context);
            $this->getEventDispatcher()->queueEvent($event);
        }
        
        return $this;
    }

    /**
     * Setter to property action
     * 
     * @param string $action
     * 
     * @return $this
     */
    public function setAction(string $action): self
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
    public function setContext($context): self
    {
        $this->context = $context;
        return $this;
    }
    
    /**
     * Add a new item into the notifyHeap list
     * For backward compatibility with tests
     * 
     * @param string $action
     * @param mixed $context
     * 
     * @return void
     */
    public function addNotifyHeap(string $action, $context)
    {
        $event = new \BFW\Events\Event($action, $context);
        $this->getEventDispatcher()->queueEvent($event);
    }
}

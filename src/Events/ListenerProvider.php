<?php

namespace BFW\Events;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Manages event listeners for the BFW framework
 * Replaces the observer attachment mechanism from Subject class
 */
class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var callable[][] $listeners Map of event classes to listeners
     */
    protected $listeners = [];
    
    /**
     * @var callable[] $globalListeners Listeners that listen to all events
     */
    protected $globalListeners = [];
    
    /**
     * Add a listener for a specific event class or action name
     * 
     * @param string $eventIdentifier Event class name or action name
     * @param callable $listener The listener callable
     * @return $this
     */
    public function addListener(string $eventIdentifier, callable $listener): self
    {
        if (!isset($this->listeners[$eventIdentifier])) {
            $this->listeners[$eventIdentifier] = [];
        }
        
        $this->listeners[$eventIdentifier][] = $listener;
        return $this;
    }
    
    /**
     * Remove a listener for a specific event class or action name
     * 
     * @param string $eventIdentifier Event class name or action name
     * @param callable $listener The listener callable to remove
     * @return $this
     */
    public function removeListener(string $eventIdentifier, callable $listener): self
    {
        if (!isset($this->listeners[$eventIdentifier])) {
            return $this;
        }
        
        $key = array_search($listener, $this->listeners[$eventIdentifier], true);
        if ($key !== false) {
            unset($this->listeners[$eventIdentifier][$key]);
        }
        
        return $this;
    }
    
    /**
     * Add a global listener that receives all events
     * 
     * @param callable $listener The listener callable
     * @return $this
     */
    public function addGlobalListener(callable $listener): self
    {
        $this->globalListeners[] = $listener;
        return $this;
    }
    
    /**
     * Remove a global listener
     * 
     * @param callable $listener The listener callable to remove
     * @return $this
     */
    public function removeGlobalListener(callable $listener): self
    {
        $key = array_search($listener, $this->globalListeners, true);
        if ($key !== false) {
            unset($this->globalListeners[$key]);
        }
        
        return $this;
    }
    
    /**
     * Get all listeners
     * 
     * @return callable[][]
     */
    public function getAllListeners(): array
    {
        return $this->listeners;
    }
    
    /**
     * Get global listeners
     * 
     * @return callable[]
     */
    public function getGlobalListeners(): array
    {
        return $this->globalListeners;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        $listeners = [];
        
        // Add global listeners first
        $listeners = array_merge($listeners, $this->globalListeners);
        
        // Add listeners for the exact event class
        $eventClass = get_class($event);
        if (isset($this->listeners[$eventClass])) {
            $listeners = array_merge($listeners, $this->listeners[$eventClass]);
        }
        
        // Add listeners for the action name (for backward compatibility)
        if ($event instanceof Event) {
            $action = $event->getAction();
            if (isset($this->listeners[$action])) {
                $listeners = array_merge($listeners, $this->listeners[$action]);
            }
        }
        
        return $listeners;
    }
}
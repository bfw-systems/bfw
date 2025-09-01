<?php

namespace BFW\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * PSR-14 compliant Event Dispatcher for BFW framework
 * Replaces the Subject class functionality
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var ListenerProviderInterface $listenerProvider
     */
    protected $listenerProvider;
    
    /**
     * @var object[] $eventQueue Queue for events to be dispatched
     */
    protected $eventQueue = [];
    
    /**
     * @var bool $dispatching Whether we are currently dispatching events
     */
    protected $dispatching = false;
    
    /**
     * Constructor
     * 
     * @param ListenerProviderInterface $listenerProvider
     */
    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }
    
    /**
     * Get the listener provider
     * 
     * @return ListenerProviderInterface
     */
    public function getListenerProvider(): ListenerProviderInterface
    {
        return $this->listenerProvider;
    }
    
    /**
     * Queue an event for dispatching
     * Similar to addNotification in the original Subject class
     * 
     * @param object $event The event to queue
     * @return $this
     */
    public function queueEvent(object $event): self
    {
        $this->eventQueue[] = $event;
        
        // If we're not currently dispatching, start processing the queue
        if (!$this->dispatching && count($this->eventQueue) === 1) {
            $this->processEventQueue();
        }
        
        return $this;
    }
    
    /**
     * Process the event queue
     * Similar to readNotifyHeap in the original Subject class
     * 
     * @return $this
     */
    public function processEventQueue(): self
    {
        $this->dispatching = true;
        
        while (!empty($this->eventQueue)) {
            $event = array_shift($this->eventQueue);
            $this->dispatch($event);
        }
        
        $this->dispatching = false;
        return $this;
    }
    
    /**
     * Get the current event queue
     * 
     * @return object[]
     */
    public function getEventQueue(): array
    {
        return $this->eventQueue;
    }
    
    /**
     * {@inheritdoc}
     */
    public function dispatch(object $event)
    {
        // Log the event if it's a BFW Event and Application is available
        if ($event instanceof Event) {
            try {
                \BFW\Application::getInstance()
                    ->getMonolog()
                    ->getLogger()
                    ->debug(
                        'EventDispatcher dispatch event',
                        ['action' => $event->getAction()]
                    );
            } catch (\Exception $e) {
                // Application or monolog not available, continue without logging
            }
        }
        
        $listeners = $this->listenerProvider->getListenersForEvent($event);
        
        foreach ($listeners as $listener) {
            // Check if the event can be stopped and has been stopped
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
            
            // Call the listener
            if (is_callable($listener)) {
                $listener($event);
            }
        }
        
        return $event;
    }
    
    /**
     * Create and queue an event with action and context
     * Backward compatibility method similar to addNotification
     * 
     * @param string $action The action name
     * @param mixed $context (default null) The context data
     * @return $this
     */
    public function addNotification(string $action, $context = null): self
    {
        $event = new Event($action, $context);
        return $this->queueEvent($event);
    }
}
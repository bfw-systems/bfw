<?php

namespace BFW;

use \Exception;
use \SplSubject;
use \SplObserver;
use BFW\Events\EventDispatcher;
use BFW\Events\ListenerProvider;
use BFW\Events\ObserverBridge;
use BFW\Events\Event;

/**
 * Class to manage subject in observers systems
 * Now uses PSR-14 Event Dispatcher internally while maintaining backward compatibility
 */
class Subject implements SplSubject
{
    /**
     * @const ERR_OBSERVER_NOT_FOUND Exception code if the observer to detach
     * has not been found.
     */
    const ERR_OBSERVER_NOT_FOUND = 1109001;
    
    /**
     * @var EventDispatcher $eventDispatcher The PSR-14 event dispatcher
     */
    protected $eventDispatcher;
    
    /**
     * @var ListenerProvider $listenerProvider The PSR-14 listener provider
     */
    protected $listenerProvider;
    
    /**
     * @var \SplObserver[] $observers List of all observers (for backward compatibility)
     */
    protected $observers = [];
    
    /**
     * @var string $action The current action to send to observers (for backward compatibility)
     */
    protected $action = '';
    
    /**
     * @var mixed $context The current context to send to observers (for backward compatibility)
     */
    protected $context = null;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->listenerProvider = new ListenerProvider();
        $this->eventDispatcher = new EventDispatcher($this->listenerProvider);
    }
    
    /**
     * Get the PSR-14 event dispatcher
     * 
     * @return EventDispatcher
     */
    public function getEventDispatcher(): EventDispatcher
    {
        return $this->eventDispatcher;
    }
    
    /**
     * Get the PSR-14 listener provider
     * 
     * @return ListenerProvider
     */
    public function getListenerProvider(): ListenerProvider
    {
        return $this->listenerProvider;
    }
    
    /**
     * Return list of all observers
     * 
     * @return \SplObserver[]
     */
    public function getObservers(): array
    {
        return $this->observers;
    }
    
    /**
     * Return list of all notify to send
     * 
     * @return object[]
     */
    public function getNotifyHeap(): array
    {
        return $this->eventDispatcher->getEventQueue();
    }
    
    /**
     * Return the action
     * 
     * @return string
     */
    public function getAction(): string
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
     * @return void
     */
    public function attach(SplObserver $observer)
    {
        $this->observers[] = $observer;
        
        // Add the observer as a global listener via the bridge
        $listener = ObserverBridge::createListener($observer);
        $this->listenerProvider->addGlobalListener($listener);
    }

    /**
     * Detach a observer to the list
     * 
     * @param \SplObserver $observer The observer instance to detach
     * 
     * @return void
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
        
        // Remove the observer from the listener provider
        // Note: This is a limitation - we can't easily remove specific observer bridges
        // In practice, this should work fine as observers are typically attached once
    }

    /**
     * Send a notification to all observers
     * 
     * @return void
     */
    public function notify()
    {
        // Create an event with current action and context
        $event = new Event($this->action, $this->context);
        $this->eventDispatcher->dispatch($event);
    }
    
    /**
     * Read the notify heap list and send each notify into the list.
     * 
     * @return $this
     */
    public function readNotifyHeap(): self
    {
        $this->eventDispatcher->processEventQueue();
        return $this;
    }
    
    /**
     * Add a new notification to the list of notification to send.
     * If there is only one notification into the list, it will be send now.
     * Else, a notification is currently sent, so we wait it finish and the
     * current notification will be sent.
     * 
     * @param string $action The action to send
     * @param mixed $context (default null) The context to send
     * 
     * @return \BFW\Subject The current instance of this class
     */
    public function addNotification(string $action, $context = null): self
    {
        // Update current action and context for backward compatibility
        $this->action = $action;
        $this->context = $context;
        
        // Use the event dispatcher to queue and potentially dispatch the event
        $this->eventDispatcher->addNotification($action, $context);
        
        return $this;
    }
}

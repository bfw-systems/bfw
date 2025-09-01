<?php

namespace BFW\Events;

use SplSubject;
use SplObserver;

/**
 * Bridge subject that provides the old SplSubject interface for events
 * Allows existing SplObserver implementations to work with PSR-14 events
 */
class SubjectBridge implements SplSubject
{
    /**
     * @var object $event The PSR-14 event
     */
    protected $event;
    
    /**
     * Constructor
     * 
     * @param object $event The PSR-14 event
     */
    public function __construct(object $event)
    {
        $this->event = $event;
    }
    
    /**
     * Get the action name from the event
     * Provides backward compatibility with Subject::getAction()
     * 
     * @return string
     */
    public function getAction(): string
    {
        if ($this->event instanceof Event) {
            return $this->event->getAction();
        }
        
        // For non-BFW events, return the class name
        return get_class($this->event);
    }
    
    /**
     * Get the context from the event
     * Provides backward compatibility with Subject::getContext()
     * 
     * @return mixed
     */
    public function getContext()
    {
        if ($this->event instanceof Event) {
            return $this->event->getContext();
        }
        
        // For non-BFW events, return the event itself as context
        return $this->event;
    }
    
    /**
     * Get the original event
     * 
     * @return object
     */
    public function getEvent(): object
    {
        return $this->event;
    }
    
    /**
     * {@inheritdoc}
     * Not implemented - this is a read-only bridge
     */
    public function attach(SplObserver $observer)
    {
        throw new \BadMethodCallException(
            'SubjectBridge is read-only. Use EventDispatcher to manage listeners.'
        );
    }
    
    /**
     * {@inheritdoc}
     * Not implemented - this is a read-only bridge
     */
    public function detach(SplObserver $observer)
    {
        throw new \BadMethodCallException(
            'SubjectBridge is read-only. Use EventDispatcher to manage listeners.'
        );
    }
    
    /**
     * {@inheritdoc}
     * Not implemented - this is a read-only bridge
     */
    public function notify()
    {
        throw new \BadMethodCallException(
            'SubjectBridge is read-only. Use EventDispatcher to dispatch events.'
        );
    }
}
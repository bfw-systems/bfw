<?php

namespace BFW\Events;

use SplObserver;

/**
 * Bridge class to convert SplObserver to PSR-14 listener
 * Provides backward compatibility for existing observers
 */
class ObserverBridge
{
    /**
     * @var SplObserver $observer The observer to bridge
     */
    protected $observer;
    
    /**
     * Constructor
     * 
     * @param SplObserver $observer The observer to bridge
     */
    public function __construct(SplObserver $observer)
    {
        $this->observer = $observer;
    }
    
    /**
     * Get the wrapped observer
     * 
     * @return SplObserver
     */
    public function getObserver(): SplObserver
    {
        return $this->observer;
    }
    
    /**
     * Create a callable that bridges to the observer's update method
     * 
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (object $event) {
            // Create a bridge subject that provides the old interface
            $bridgeSubject = new SubjectBridge($event);
            $this->observer->update($bridgeSubject);
        };
    }
    
    /**
     * Static helper to create a listener from an SplObserver
     * 
     * @param SplObserver $observer
     * @return callable
     */
    public static function createListener(SplObserver $observer): callable
    {
        $bridge = new self($observer);
        return $bridge->getCallable();
    }
}
<?php

namespace BFW\Events;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Base event class for BFW framework
 * Replaces the action/context pattern from the Subject class
 */
class Event implements StoppableEventInterface
{
    /**
     * @var string $action The action name (event type)
     */
    protected $action;
    
    /**
     * @var mixed $context The context data for this event
     */
    protected $context;
    
    /**
     * @var bool $propagationStopped Whether event propagation is stopped
     */
    protected $propagationStopped = false;
    
    /**
     * Constructor
     * 
     * @param string $action The action name
     * @param mixed $context (default null) The context data
     */
    public function __construct(string $action, $context = null)
    {
        $this->action = $action;
        $this->context = $context;
    }
    
    /**
     * Get the action name
     * 
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }
    
    /**
     * Get the context data
     * 
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }
    
    /**
     * Set the context data
     * 
     * @param mixed $context
     * @return $this
     */
    public function setContext($context): self
    {
        $this->context = $context;
        return $this;
    }
    
    /**
     * Stop event propagation
     * 
     * @return $this
     */
    public function stopPropagation(): self
    {
        $this->propagationStopped = true;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
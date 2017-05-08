<?php

namespace BFW;

use \SplSubject;
use \SplObserver;

/**
 * Class to manage subject in observers systems
 */
class Subjects implements SplSubject
{
    /**
     * @var \SplObserver[] $observers List of all observers
     */
    protected $observers = [];
    
    /**
     * @var string $action The action to send to observers
     */
    protected $action = '';
    
    /**
     * @var mixed $context The context to send to observers
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
     * Return the action
     * 
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Define the action
     * 
     * @param string $action The new action
     * 
     * @return \BFW\Subjects The current instance of this class
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
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
     * Define the context
     * 
     * @param mixed $context The new context
     * 
     * @return \BFW\Subjects The current instance of this class
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Attach a new observer to the list
     * 
     * @param \SplObserver $observer The new observer
     * 
     * @return \BFW\Subjects The current instance of this class
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
     * @return \BFW\Subjects The current instance of this class
     */
    public function detach(SplObserver $observer)
    {
        $key = array_search($observer, $this->observers, true);

        if ($key !== false) {
            unset($this->observers[$key]);
        }

        return $this;
    }

    /**
     * Send a notification to all observers
     * 
     * @return \BFW\Subjects The current instance of this class
     */
    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }

        return $this;
    }
    
    /**
     * Send a notification to all observers with an action
     * 
     * @param string $action The action to send
     * 
     * @return \BFW\Subjects The current instance of this class
     */
    public function notifyAction($action)
    {
        $this->action = $action;
        $this->notify();
        
        return $this;
    }
}

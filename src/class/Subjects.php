<?php

namespace BFW;

use \SplSubject;

class Subjects implements SplSubject
{
    protected $observers = [];
    protected $action = '';
    protected $context = null;

    public function attach(SplObserver $observer)
    {
        $this->observers[] = $observer;

        return $this;
    }

    public function detach(SplObserver $observer)
    {
        $key = array_search($observer, $this->observers, true);

        if ($key !== false) {
            unset($this->observers[$key]);
        }

        return $this;
    }

    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }

        return $this;
    }
    
    public function notifyAction($action)
    {
        $this->action = $action;
        $this->notify();
        
        return $this;
    }
    
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }
    
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }
    
    public function getAction()
    {
        return $this->action;
    }
    
    public function getContext()
    {
        return $this->context;
    }
}

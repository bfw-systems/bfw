<?php

namespace BFW;

use \SplSubject;

class Subjects implements SplSubject
{
    protected $observers = [];

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

    public function notify($action = '')
    {
        foreach ($this->observers as $observer) {
            $observer->update($this, $action);
        }

        return $this;
    }
}

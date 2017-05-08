<?php

namespace BFW\test\unit\mocks;

/**
 * Mock for Observer class
 */
class Observer implements \SplObserver
{
    /**
     * {@inheritdoc}
     * Echo the action received
     */
    public function update(\SplSubject $subject)
    {
        echo $subject->getAction()."\n";
    }
}

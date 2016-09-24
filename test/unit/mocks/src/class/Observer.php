<?php

namespace BFW\test\unit\mocks;

class Observer implements \SplObserver
{
    public function update(\SplSubject $subject)
    {
        echo $subject->getAction()."\n";
    }
}

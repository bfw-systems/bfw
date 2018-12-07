<?php

namespace BFW\Test\Helpers;

/**
 * Used by unit test for test on system which use the observer pattern
 */
class ObserverArray implements \SplObserver
{
    /**
     * @var array $actionReceived List of all actions received 
     */
    protected $actionReceived = [];
    
    /**
     * @var array $updateReceived List of all update received 
     */
    protected $updateReceived = [];
    
    /**
     * Getter to actionReceived property
     * 
     * @return array
     */
    public function getActionReceived(): array
    {
        return $this->actionReceived;
    }
    
    /**
     * Getter to updateReceived property
     * 
     * @return array
     */
    public function getUpdateReceived(): array
    {
        return $this->updateReceived;
    }
    
    /**
     * {@inheritdoc}
     * Save the action received into an array
     */
    public function update(\SplSubject $subject)
    {
        $this->actionReceived[] = $subject->getAction();
        
        $this->updateReceived[] = new class(
            $subject->getAction(),
            $subject->getContext()
        ) {
            public $action;
            public $context;
            
            public function __construct($action, $context) {
                $this->action  = $action;
                $this->context = $context;
            }
        };
    }
}

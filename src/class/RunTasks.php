<?php

namespace BFW;

class RunTasks extends Subject
{
    /**
     * @var object[] $runSteps All steps used for run the application
     */
    protected $runSteps;
    
    /**
     * @var string $notifyPrefix A prefix to use for task name
     */
    protected $notifyPrefix;
    
    /**
     * Constructor
     * 
     * @param object[] $runSteps All step to call
     * @param string $notifyPrefix The prefix to use for task name
     */
    public function __construct(array $runSteps, string $notifyPrefix)
    {
        $this->runSteps     = $runSteps;
        $this->notifyPrefix = $notifyPrefix;
    }
    
    /**
     * Getter to access to the run step array
     * 
     * @return object[]
     */
    public function getRunSteps(): array
    {
        return $this->runSteps;
    }
    
    /**
     * Setter to re-define the run step array
     * 
     * @param object[] $runSteps The new list of run steps
     * 
     * @return $this
     */
    public function setRunSteps(array $runSteps): self
    {
        $this->runSteps = $runSteps;
        return $this;
    }
    
    /**
     * Add a new run step to the list
     * 
     * @param string $name          The name of the new run step
     * @param object $runStepsToAdd The run step to add
     * 
     * @return $this
     */
    public function addToRunSteps(string $name, $runStepsToAdd): self
    {
        $this->runSteps[$name] = $runStepsToAdd;
        return $this;
    }
    
    /**
     * Getter to property notifyPrefix
     * 
     * @return string
     */
    public function getNotifyPrefix(): string
    {
        return $this->notifyPrefix;
    }
    
    /**
     * Setter to re-define the notifyPrefix
     * 
     * @param string $notifyPrefix
     * 
     * @return $this
     */
    public function setNotifyPrefix(string$notifyPrefix): self
    {
        $this->notifyPrefix = $notifyPrefix;
        return $this;
    }
    
    /**
     * Run all steps declared and notify for each step
     * Call the callback if declared for each step
     * 
     * @return void
     */
    public function run()
    {
        $prefix = $this->notifyPrefix;
        
        $this->addNotification($prefix.'_start_run_tasks');
        
        foreach ($this->runSteps as $actionName => $stepInfos) {
            $context = null;
            if ($stepInfos->context !== null) {
                $context = $stepInfos->context;
            }
            
            if ($stepInfos->callback === null) {
                $this->addNotification($prefix.'_exec_'.$actionName, $context);
                continue;
            }
            
            $this->addNotification($prefix.'_run_'.$actionName, $context);

            if (is_callable($stepInfos->callback)) {
                $callback = $stepInfos->callback;
                $callback();
            }

            $this->addNotification($prefix.'_done_'.$actionName, $context);
        }
        
        $this->addNotification($prefix.'_end_run_tasks');
    }
    
    /**
     * Send a notification to all observers connected to the subject
     * 
     * @param string $action The action name
     * @param mixed $context (default null) The context to pass to the subject
     * 
     * @return void
     */
    public function sendNotify(string $action, $context = null)
    {
        \BFW\Application::getInstance()
            ->getMonolog()
            ->getLogger()
            ->debug(
                'RunTask notify',
                [
                    'prefix' => $this->notifyPrefix,
                    'action' => $action
                ]
            )
        ;
        
        $this->addNotification($action, $context);
    }
    
    /**
     * Generate the anonymous class with the structure to use for each item
     * 
     * @param mixed $context The context to add to the notify
     * @param callable|null $callback The callback to call when
     *  the task is runned
     * 
     * @return object
     */
    public static function generateStepItem($context = null, $callback = null)
    {
        return new class ($context, $callback) {
            public $context;
            public $callback;
            
            public function __construct($context, $callback)
            {
                $this->context  = $context;
                $this->callback = $callback;
            }
        };
    }
}

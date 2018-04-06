<?php

namespace BFW;

class RunTasks extends Subjects
{
    /**
     * @var \stdClass[] $runSteps All steps used for run the application
     */
    protected $runSteps;
    
    /**
     * @var string $notifyPrefix A prefix to use for task name
     */
    protected $notifyPrefix;
    
    /**
     * Constructor
     * 
     * @param \stdClass[] $runSteps All step to call
     * @param string $notifyPrefix The prefix to use for task name
     */
    public function __construct($runSteps, $notifyPrefix)
    {
        $this->runSteps     = $runSteps;
        $this->notifyPrefix = $notifyPrefix;
    }
    
    /**
     * Getter to access to the run step array
     * 
     * @return \stdClass[]
     */
    public function getRunSteps()
    {
        return $this->runSteps;
    }
    
    /**
     * Setter to re-define the run step array
     * 
     * @param \stdClass[] $runSteps The new list of run steps
     * 
     * @return $this
     */
    public function setRunSteps($runSteps)
    {
        $this->runSteps = $runSteps;
        return $this;
    }
    
    /**
     * Add a new run step to the list
     * 
     * @param string    $name          The name of the new run step
     * @param \stdClass $runStepsToAdd The run step to add
     * 
     * @return $this
     */
    public function addToRunSteps($name, \stdClass $runStepsToAdd)
    {
        $this->runSteps[(string) $name] = $runStepsToAdd;
        return $this;
    }
    
    /**
     * Getter to property notifyPrefix
     * 
     * @return string
     */
    public function getNotifyPrefix()
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
    public function setNotifyPrefix($notifyPrefix)
    {
        $this->notifyPrefix = (string) $notifyPrefix;
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
            if (property_exists($stepInfos, 'context')) {
                $context = $stepInfos->context;
            }
            
            if (!property_exists($stepInfos, 'callback')) {
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
    public function sendNotify($action, $context = null)
    {
        $this->addNotification($action, $context);
    }
}

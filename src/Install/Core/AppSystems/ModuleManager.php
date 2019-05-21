<?php

namespace BFW\Install\Core\AppSystems;

use \BFW\Core\AppSystems\AbstractSystem;
use BFW\Install\ModuleManager as Manager;

class ModuleManager extends AbstractSystem
{
    /**
     * @var \BFW\Install\ModuleManager
     */
    protected $manager;

    /**
     * Initialize the ModuleManager system
     */
    public function __construct()
    {
        $this->manager = new Manager;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return $this
     */
    public function __invoke()
    {
        return $this->manager;
    }
    
    /**
     * Getter accessor to property manager
     * 
     * @return \BFW\Install\ModuleManager
     */
    public function getManager(): Manager
    {
        return $this->manager;
    }
    
    /**
     * {@inheritdoc}
     */
    public function toRun(): bool
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     * Run install of all modules
     */
    public function run()
    {
        $this->manager->doAction();
        $this->runStatus = true;
    }
}

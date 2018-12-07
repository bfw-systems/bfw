<?php

namespace BFW\Core\AppSystems;

interface SystemInterface
{
    /**
     * To know if the run method should be called
     * 
     * @return boolean
     */
    public function toRun(): bool;
    
    /**
     * Return the runStatus value.
     * To know if the run method has already been called.
     * 
     * @return boolean
     */
    public function isRun(): bool;
    
    /**
     * To run the core system
     * 
     * @return void
     */
    public function run();
}

<?php

namespace BFW;

/**
 * Interface for BFW modules that defines the contract for module execution
 * 
 * This interface provides a better alternative to the previous ModuleRunInterface
 * name as it allows for future expansion beyond just running modules.
 */
interface ModuleInterface
{
    /**
     * Execute the module's main functionality
     * 
     * This method is called by the framework when the module needs to be executed.
     * Module authors should implement their main logic in this method.
     * 
     * @return void
     */
    public function run(): void;
}
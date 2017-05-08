<?php

namespace BFW\test\unit\mocks;

/**
 * Mock for Module class
 * (Wait, again ?! oO .... @TODO)
 */
class ModuleRunnerFile extends \BFW\Module
{
    /**
     * Method to call the protected method getRunnerFile
     * 
     * @return string
     */
    public function callGetRunnerFile()
    {
        return parent::getRunnerFile();
    }
}

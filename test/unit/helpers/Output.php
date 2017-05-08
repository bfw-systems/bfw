<?php

namespace BFW\test\helpers;

/**
 * Used to get ouput with atoum when native php function is mocked
 * Because mock and closure does not work together.
 */
class Output
{
    /**
     * @var string $output The catched output
     */
    protected static $output = '';
    
    /**
     * Start to catch output
     * 
     * @return void
     */
    public function startCatchOutput()
    {
        ob_start();
        ob_clean();
    }
    
    /**
     * Stop to catch output
     * 
     * @return void
     */
    public function endCatchOutput()
    {
        self::$output = ob_get_contents();
        ob_end_clean();
    }
    
    /**
     * Get the catched output
     * 
     * @return string
     */
    public function getOutput()
    {
        return self::$output;
    }
}


<?php

namespace BFW\test\helpers;

/**
 * Used to get ouput with atoum when native php function is mocked
 * Because mock and closure does not work together.
 */
class Output
{
    protected static $output = '';
    
    public function startCatchOutput()
    {
        ob_start();
        ob_clean();
    }
    
    public function endCatchOutput()
    {
        self::$output = ob_get_contents();
        ob_end_clean();
    }
    
    public function getOutput()
    {
        return self::$output;
    }
}


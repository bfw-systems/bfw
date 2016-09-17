<?php

namespace BFW\Helpers;

use \Exception;

/**
 * Helpers to manage constants
 */
class Constants
{
    /**
     * Create a new constant if not exist
     * 
     * @param string $cstName The constant's name
     * @param mixed $cstValue The constant's value
     * 
     * @throws Exception If the constant is already defined
     */
    public static function create($cstName, $cstValue)
    {
        if (defined($cstName)) {
            throw new Exception('Constant '.$cstName.' already defined.');
        }
        
        define($cstName, $cstValue);
    }
}

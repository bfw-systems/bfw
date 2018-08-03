<?php

namespace BFW\Helpers;

use \Exception;

/**
 * Helpers to manage constants
 */
class Constants
{
    /**
     * @const ERR_ALREADY_DEFINED Exception code if the constant is already
     * defined.
     */
    const ERR_ALREADY_DEFINED = 1602001;
    
    /**
     * Create a new constant if not exist
     * 
     * @param string $cstName The constant's name
     * @param mixed $cstValue The constant's value
     * 
     * @return void
     * 
     * @throws Exception If the constant is already defined
     */
    public static function create($cstName, $cstValue)
    {
        if (defined($cstName)) {
            throw new Exception(
                'The constant '.$cstName.' is already defined.',
                self::ERR_ALREADY_DEFINED
            );
        }
        
        define($cstName, $cstValue);
    }
}

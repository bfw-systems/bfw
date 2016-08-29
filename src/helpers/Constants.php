<?php

namespace BFW\Helpers;

use \Exception;

class Constants
{
    public static function create($cstName, $cstValue)
    {
        if(defined($cstName)) {
            throw new Exception('Constant '.$cstName.' already defined.');
        }
        
        define($cstName, $cstValue);
    }
}

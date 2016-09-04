<?php

namespace BFW\test\helpers;

/**
 * Used to catch error with atoum when native php function is mocked
 * Because mock and closure does not work together.
 */
class Errors
{
    protected static $infos;
    
    public function __construct()
    {
        self::$infos = (object) [
            'type' => '',
            'message' => ''
        ];
    }
    
    public function startCatchError()
    {
        set_error_handler([$this, 'error']);
    }
    
    public function endCatchError()
    {
        restore_error_handler();
    }
    
    public static function error(
        $erreurType,
        $errMsg,
        $errFile,
        $errLine,
        $backtrace
    ) {
        self::$infos->type    = $erreurType;
        self::$infos->message = $errMsg;
    }
    
    public function setError()
    {
        trigger_error(self::$infos->message, self::$infos->type);
    }
}


<?php

namespace BFW\test\helpers;

/**
 * Used to catch error with atoum when native php function is mocked
 * Because mock and closure does not work together.
 */
class Errors
{
    /**
     * @var \stdClass $infos Information about the error
     */
    protected static $infos;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        self::$infos = (object) [
            'type'    => '',
            'message' => ''
        ];
    }
    
    /**
     * Start to catch all errors
     * 
     * @return void
     */
    public function startCatchError()
    {
        set_error_handler([$this, 'error']);
    }
    
    /**
     * Stop to catch all errors
     * 
     * @return void
     */
    public function endCatchError()
    {
        restore_error_handler();
    }
    
    /**
     * Call when an error is catched
     * 
     * @param string  $errType : Human readable error severity
     * @param string  $errMsg : Error message
     * @param string  $errFile : File where the error is triggered
     * @param integer $errLine : Line where the error is triggered
     * @param array   $backtrace : Error/exception backtrace
     * 
     * @return void
     */
    public static function error(
        $errType,
        $errMsg,
        $errFile,
        $errLine,
        $backtrace
    ) {
        self::$infos->type    = $errType;
        self::$infos->message = $errMsg;
    }
    
    /**
     * Trigger an error to php
     * 
     * @çeturn void
     */
    public function setError()
    {
        trigger_error(self::$infos->message, self::$infos->type);
    }
}


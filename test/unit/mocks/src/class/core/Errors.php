<?php

namespace BFW\Core\test\unit\mocks;

/**
 * Mock for Errors class
 */
class Errors extends \BFW\Core\Errors
{
    /**
     * @var callable The error_error_handler used before mine
     */
    public static $lastRenderCallInfos;
    
    /**
     * Constructor
     * Restore error and exception handler to remove handlers define by
     * the BFW Errors class
     */
    public function __construct()
    {
        parent::__construct();
        
        restore_error_handler();
        restore_exception_handler();
    }
    
    /**
     * Method to call the protected method defineErrorHandler
     * 
     * @return callable The error render declared by defineErrorHandler
     */
    public function callDefineErrorHandler()
    {
        parent::defineErrorHandler();
        
        //Get last render declared
        $lastErrorRender = set_error_handler(['\BFW\Core\test\unit\mocks\Errors', 'mockRender']);
        restore_error_handler();
        
        //Disable handler create in defineErrorHandler()
        restore_error_handler();
        
        return $lastErrorRender;
    }
    
    /**
     * Method to call the protected method defineExceptionHandler
     * 
     * @return callable The exception render declared by defineExceptionHandler
     */
    public function callDefineExceptionHandler()
    {
        parent::defineExceptionHandler();
        
        //Get last render declared
        $lastExceptionRender = set_exception_handler(['\BFW\Core\test\unit\mocks\Errors', 'mockRender']);
        restore_exception_handler();
        
        //Disable handler create in defineErrorHandler()
        restore_exception_handler();
        
        return $lastExceptionRender;
    }
    
    /**
     * Method to call the protected method getErrorType
     * 
     * @param int $errSeverity The error severity with PHP constant
     * 
     * @return string
     */
    public function callGetErrorType($errSeverity)
    {
        return self::getErrorType($errSeverity);
    }
    
    /**
     * Error or exception handler used by this mock
     * 
     * @param string  $errType : Human readable error severity
     * @param string  $errMsg : Error/exception message
     * @param string  $errFile : File where the error/exception is triggered
     * @param integer $errLine : Line where the error/exception is triggered
     * @param array   $backtrace : Error/exception backtrace
     * 
     * @return void
     */
    public static function mockRender(
        $errType,
        $errMsg,
        $errFile,
        $errLine,
        $backtrace
    ) {
        self::$lastRenderCallInfos = (object) [
            'errType'   => $errType,
            'errMsg'    => $errMsg,
            'errFile'   => $errFile,
            'errLine'   => $errLine,
            'backtrace' => $backtrace
        ];
    }
    
    /**
     * {@inheritdoc}
     * Do nothing for the mock
     */
    protected static function saveIntoPhpLog(
        $errType,
        $errMsg,
        $errFile,
        $errLine
    ) {
        return;
    }
}

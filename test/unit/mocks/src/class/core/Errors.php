<?php

namespace BFW\Core\test\unit\mocks;

class Errors extends \BFW\Core\Errors
{
    public static $lastRenderCallInfos;
    
    public function __construct()
    {
        parent::__construct();
        
        restore_error_handler();
        restore_exception_handler();
    }
    
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
    
    public function callGetErrorType($errSeverity)
    {
        return self::getErrorType($errSeverity);
    }
    
    public static function mockRender(
        $erreurType,
        $errMsg,
        $errFile,
        $errLine,
        $backtrace
    ) {
        self::$lastRenderCallInfos = (object) [
            'erreurType' => $erreurType,
            'errMsg' => $errMsg,
            'errFile' => $errFile,
            'errLine' => $errLine,
            'backtrace' => $backtrace
        ];
    }
    
    protected static function saveIntoPhpLog(
        $errType,
        $errMsg,
        $errFile,
        $errLine
    ) {
        return;
    }
}

<?php

namespace BFW\Core\test\unit\mocks;

use \BFW\test\unit\mocks\ApplicationForceConfig as MockApp;

class Errors extends \BFW\Core\Errors
{
    public static $lastRenderCallInfos;
    
    public function __construct(\BFW\Application $app)
    {
        parent::__construct($app);
        
        restore_error_handler();
        restore_exception_handler();
    }
    
    protected static function getApp()
    {
        parent::getApp();
        
        if(is_null(self::$app)) {
            self::$app = MockApp::getInstance();
        }
        
        return self::$app;
    }
    
    public function removeAppInstance()
    {
        self::$app = null;
    }
    
    public function callGetApp()
    {
        return self::getApp();
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
}

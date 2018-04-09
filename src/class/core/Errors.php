<?php

namespace BFW\Core;

/**
 * Class used to have a personnal message/page for errors and exceptions
 */
class Errors
{
    /**
     * Constructeur
     */
    public function __construct()
    {
        //Find and create the handler for errors
        $this->defineErrorHandler();
        
        //Find and create the handler for exceptions
        $this->defineExceptionHandler();
    }
    
    /**
     * Find and create the handler for errors
     * 
     * @return void
     */
    protected function defineErrorHandler()
    {
        $errorRender = $this->obtainErrorRender();
        
        //If not render to use
        if ($errorRender === false) {
            return;
        }

        //add the handler for errors
        set_error_handler([$this, 'errorHandler']);
    }
    
    /**
     * Find and create the handler for exceptions
     * 
     * @return type
     */
    protected function defineExceptionHandler()
    {
        $exceptionRender = $this->obtainExceptionRender();
        
        //If not render to use
        if ($exceptionRender === false) {
            return;
        }
        
        //add the handler for exceptions
        set_exception_handler([$this, 'exceptionHandler']);
    }
    
    /**
     * Get the error render from config for cli or default
     * 
     * @return boolean|array Render infos
     *  Boolean : false if no render to use
     *  Array   : Infos from config
     */
    protected function obtainErrorRender()
    {
        $app        = \BFW\Application::getInstance();
        $renderFcts = $app->getConfig()->getValue('errorRenderFct');
        
        return $this->defineRenderToUse($renderFcts);
    }
    
    /**
     * Get the exception render from config for cli or default
     * 
     * @return boolean|array Render infos
     *  Boolean : false if no render to use
     *  Array   : Infos from config
     */
    protected function obtainExceptionRender()
    {
        $app        = \BFW\Application::getInstance();
        $renderFcts = $app->getConfig()->getValue('exceptionRenderFct');
        
        return $this->defineRenderToUse($renderFcts);
    }
    
    /**
     * Find the render to use with the config
     * If cli render is not define, it's use the default render.
     * 
     * @param array $renderConfig : Render infos from config
     * 
     * @return boolean|array : Render to use
     *  Boolean : false if is no enabled or if no render is defined
     *  Array : The render to use
     */
    protected function defineRenderToUse($renderConfig)
    {
        //Check enabled
        if ($renderConfig['enabled'] === false) {
            return false;
        }
        
        //The cli render if cli mode
        if (PHP_SAPI === 'cli' && isset($renderConfig['cli'])) {
            return $renderConfig['cli'];
        }
        
        //The default render or cli if cli mode and no cli render configured
        if (isset($renderConfig['default'])) {
            return $renderConfig['default'];
        }
        
        return false;
    }
    
    /**
     * The default exception handler included in BFW
     * 
     * @param \Exception $exception : Exception informations
     * 
     * @return void
     */
    public function exceptionHandler($exception)
    {
        $errorRender = $this->obtainExceptionRender();
        
        $this->callRender(
            $errorRender,
            'Exception Uncaught', 
            $exception->getMessage(), 
            $exception->getFile(), 
            $exception->getLine(), 
            $exception->getTrace(),
            $exception->getCode()
        );
    }
    
    /**
     * The default error handler included in BFW
     * 
     * @param integer $errSeverity : Error severity
     * @param string  $errMsg : Error message
     * @param string  $errFile : File where the error is triggered
     * @param integer $errLine : Line where the error is triggered
     * 
     * @return void
     */
    public function errorHandler(
        $errSeverity,
        $errMsg,
        $errFile,
        $errLine
    ) {
        $errType     = $this->obtainErrorType($errSeverity);
        $errorRender = $this->obtainErrorRender();
        
        //Call the "callRender" method for this class (or child class)
        $this->callRender(
            $errorRender,
            $errType,
            $errMsg,
            $errFile,
            $errLine,
            debug_backtrace()
        );
    }
    
    /**
     * Call the personnal class-method or function declared on config when
     * an exception or an error is triggered.
     * 
     * @param array    $renderInfos : Infos from config
     * @param string   $errType : Human readable error severity
     * @param string   $errMsg : Error/exception message
     * @param string   $errFile : File where the error/exception is triggered
     * @param integer  $errLine : Line where the error/exception is triggered
     * @param array    $backtrace : Error/exception backtrace
     * @param int|null $exceptionCode : (default null) Exception code
     * 
     * @return void
     */
    protected function callRender(
        $renderInfos,
        $errType,
        $errMsg,
        $errFile,
        $errLine,
        $backtrace,
        $exceptionCode = null
    ) {
        $this->saveIntoPhpLog($errType, $errMsg, $errFile, $errLine);
        
        $class  = $renderInfos['class'];
        $method = $renderInfos['method'];
        
        //If is a class, call "$class::$method" (compatibility 5.x)
        if (!empty($class)) {
            $class::$method(
                $errType,
                $errMsg,
                $errFile,
                $errLine,
                $backtrace,
                $exceptionCode
            );
            
            return;
        }
        
        //If is not a class, it's a function.
        $method(
            $errType,
            $errMsg,
            $errFile,
            $errLine,
            $backtrace,
            $exceptionCode
        );
    }
    
    /**
     * Save the error into the PHP log
     * 
     * @param string  $errType : Human readable error severity
     * @param string  $errMsg : Error/exception message
     * @param string  $errFile : File where the error/exception is triggered
     * @param integer $errLine : Line where the error/exception is triggered
     * 
     * @return void
     */
    protected function saveIntoPhpLog(
        $errType,
        $errMsg,
        $errFile,
        $errLine
    ) {
        error_log(
            'Error detected : '.$errType.' '.$errMsg
            .' at '.$errFile.':'.$errLine
        );
    }
    
    /**
     * Map array to have a human readable severity.
     * 
     * @see http://fr2.php.net/manual/fr/function.set-error-handler.php#113567
     * 
     * @param int $errSeverity : The error severity with PHP constant
     * 
     * @return string
     */
    protected function obtainErrorType($errSeverity)
    {
        $errorMap = [
            E_ERROR             => 'Fatal',
            E_CORE_ERROR        => 'Fatal',
            E_USER_ERROR        => 'Fatal',
            E_COMPILE_ERROR     => 'Fatal',
            E_RECOVERABLE_ERROR => 'Fatal',
            E_WARNING           => 'Warning',
            E_CORE_WARNING      => 'Warning',
            E_USER_WARNING      => 'Warning',
            E_COMPILE_WARNING   => 'Warning',
            E_PARSE             => 'Parse',
            E_NOTICE            => 'Notice',
            E_USER_NOTICE       => 'Notice',
            E_STRICT            => 'Strict',
            E_DEPRECATED        => 'Deprecated',
            E_USER_DEPRECATED   => 'Deprecated'
        ];

        //Default value if the error is not found in the map array
        $errType = 'Unknown';
        
        //Search in map array
        if (isset($errorMap[$errSeverity])) {
            $errType = $errorMap[$errSeverity];
        }
        
        return $errType;
    }
}

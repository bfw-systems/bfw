<?php

namespace BFW\Core;

/**
 * Class used to have a personnal message/page for errors and exceptions
 */
class Errors
{
    /**
     * @var \BFW\Application $app : L'instance d'Application
     */
    protected static $app = null;
    
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
        //Find the correct class to call (return the child class if extended)
        $calledClass = get_called_class();
        $errorRender = $calledClass::getErrorRender();
        
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
        //Find the correct class to call (return the child class if extended)
        $calledClass     = get_called_class();
        $exceptionRender = $calledClass::getExceptionRender();
        
        //If not render to use
        if ($exceptionRender === false) {
            return;
        }
        
        //add the handler for exceptions
        set_exception_handler([$this, 'exceptionHandler']);
    }
    
    /**
     * get the error render from config for cli or default
     * 
     * @return boolean|array Render infos
     *      Boolean : false if no render to use
     *      Array   : Infos from config
     */
    public static function getErrorRender()
    {
        $app        = \BFW\Application::getInstance();
        $renderFcts = $app->getConfig('errorRenderFct');
        
        return self::defineRenderToUse($renderFcts);
    }
    
    /**
     * get the exception render from config for cli or default
     * 
     * @return boolean|array Render infos
     *      Boolean : false if no render to use
     *      Array   : Infos from config
     */
    public static function getExceptionRender()
    {
        $app        = \BFW\Application::getInstance();
        $renderFcts = $app->getConfig('exceptionRenderFct');
        
        return self::defineRenderToUse($renderFcts);
    }
    
    /**
     * Find the render to use with the config
     * If cli render is not define, it's use the default render.
     * 
     * @param array $renderConfig : Render infos from config
     * 
     * @return boolean|array : Render to use
     *      Boolean : false is no enable or no render define
     *      Array : The render used
     */
    protected static function defineRenderToUse($renderConfig)
    {
        //Check enabled
        if ($renderConfig['active'] === false) {
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
    public static function exceptionHandler($exception)
    {
        //Get the current class (childs class if extended)
        $calledClass = get_called_class();
        $errorRender = $calledClass::getExceptionRender();
        
        //Call the "callRender" method for this class (or child class)
        $calledClass::callRender(
            $errorRender,
            'Exception Uncaught', 
            $exception->getMessage(), 
            $exception->getFile(), 
            $exception->getLine(), 
            $exception->getTrace()
        );
    }
    
    /**
     * The default error handler included in BFW
     * 
     * @param type $errSeverity : Error severity
     * @param type $errMsg : Error message
     * @param type $errFile : File where the error is triggered
     * @param type $errLine : Line where the error is triggered
     * 
     * @return void
     */
    public static function errorHandler(
        $errSeverity,
        $errMsg,
        $errFile,
        $errLine
    ) {
        //Get the current class (childs class if extended)
        $calledClass = get_called_class();
        $erreurType  = $calledClass::getErrorType($errSeverity);
        $errorRender = $calledClass::getErrorRender();
        
        //Call the "callRender" method for this class (or child class)
        $calledClass::callRender(
            $errorRender,
            $erreurType,
            $errMsg,
            $errFile,
            $errLine,
            debug_backtrace()
        );
    }
    
    /**
     * Call the personnal class-method or function declared on config where
     * an exception or an error is triggered.
     * 
     * @param array $renderInfos : Infos from config
     * @param type $erreurType : Error severity
     * @param type $errMsg : Error/exception message
     * @param type $errFile : File where the error/exception is triggered
     * @param type $errLine : Line where the error/exception is triggered
     * @param type $backtrace : Error/exception backtrace
     * 
     * @return void
     */
    protected static function callRender(
        $renderInfos,
        $erreurType,
        $errMsg,
        $errFile,
        $errLine,
        $backtrace
    ) {
        $calledClass = get_called_class();
        $calledClass::saveIntoPhpLog($erreurType, $errMsg, $errFile, $errLine);
        
        $class  = $renderInfos['class'];
        $method = $renderInfos['method'];
        
        //If is a class, call "$class::$method" (compatibility 5.x)
        if (!empty($class)) {
            $class::$method(
                $erreurType,
                $errMsg,
                $errFile,
                $errLine,
                $backtrace
            );
            
            return;
        }
        
        //If is not a class, it's a function.
        $method(
            $erreurType,
            $errMsg,
            $errFile,
            $errLine,
            $backtrace
        );
    }
    
    protected static function saveIntoPhpLog(
        $errType,
        $errMsg,
        $errFile,
        $errLine
    ) {
        error_log(
            'Error detected : '
            .$errType.' '.$errMsg
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
    protected static function getErrorType($errSeverity)
    {
        $map = [
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
        $erreurType = 'Unknown';
        
        //Search in map array
        if (isset($map[$errSeverity])) {
            $erreurType = $map[$errSeverity];
        }
        
        return $erreurType;
    }

    /**
     * The default cli render in BFW
     * 
     * @param type $erreurType : Error severity
     * @param type $errMsg : Error/exception message
     * @param type $errFile : File where the error/exception is triggered
     * @param type $errLine : Line where the error/exception is triggered
     * @param type $backtrace : Error/exception backtrace
     * 
     * @return void
     */
    public static function defaultCliErrorRender(
        $erreurType,
        $errMsg,
        $errFile,
        $errLine,
        $backtrace
    ) {
        //Create the cli message
        $msgError = $erreurType.' Error : '.$errMsg.
            ' in '.$errFile.' at line '.$errLine;
        
        //Display the message with displayMsg function
        \BFW\Helpers\Cli::displayMsg(
            $msgError,
            'white',
            'red'
        );
    }

    /**
     * The default render in BFW
     * 
     * @param type $erreurType : Error severity
     * @param type $errMsg : Error/exception message
     * @param type $errFile : File where the error/exception is triggered
     * @param type $errLine : Line where the error/exception is triggered
     * @param type $backtrace : Error/exception backtrace
     * 
     * @return void
     */
    public static function defaultErrorRender(
        $erreurType,
        $errMsg,
        $errFile,
        $errLine,
        $backtrace
    ) {
        ob_clean();

        echo '
        <!doctype html>
        <html lang="fr">
            <head>
                <title>A error is detected !</title>
                <style>
                    html {padding:0; margin:0; background-color:#e3e3e3; font-family:sans-serif; font-size: 1em; word-wrap:break-word;}
                    div {position:relative; margin:auto; width:950px; border: 1px solid #a6c9e2; top: 30px; margin-bottom:10px;}
                    p {padding:0; margin:0;}
                    p.title {font-size:1.2em; background-color:#D0DCE9; padding:10px;}
                    p.info {padding:5px; margin-top:10px; margin-bottom:10px;}
                    fieldset {border:none; background-color: white;}
                    pre {width:910px; line-height:1.5;}
                </style>
            </head>
            <body>
                <div>
                    <p class="title">Niarf, a error is detected !</p>
                    <p class="info">'.$erreurType.' Error : <strong>'.$errMsg.'</strong> in '.$errFile.' at line '.$errLine.'</p>
                    <fieldset><pre>';
                        foreach ($backtrace as $i => $info) {
                            echo '#'.$i.'  '.$info['function'];

                            if (isset($info['args']) && count($info['args']) > 0) {
                                echo '(';

                                foreach ($info['args'] as $iArgs => $args) {
                                    if ($iArgs > 0) {
                                        echo ', ';
                                    }

                                    if (is_array($args) || is_object($args)) {
                                        echo gettype($args);
                                    } elseif (is_null($args)) {
                                        echo 'null';
                                    } else {
                                        echo htmlentities($args);
                                    }
                                }

                                echo ')';
                            }

                            if (isset($info['file'], $info['line'])) {
                                echo ' called at ['.$info['file'].' line '.$info['line'].']';
                            }
                            echo "\n\n";
                        }
                    echo '</pre></fieldset>
                </div>
            <body>
        </html>
        ';

        ob_flush();
        exit;
    }
}

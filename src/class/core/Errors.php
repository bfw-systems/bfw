<?php

namespace BFW\Core;

use \BFW\Application;

class Errors
{
    protected static $app = null;
    
    public function __construct(Application $app)
    {
        self::$app   = $app;
        
        $this->defineErrorHandler();
        $this->defineExceptionHandler();
    }
    
    protected function defineErrorHandler()
    {
        $calledClass = get_called_class();
        $errorRender = $calledClass::getErrorRender();
        
        if ($errorRender === false) {
            return;
        }
        
        $errorHandlerArgs = $errorRender['method'];
        if (!empty($errorRender['class'])) {
            $errorHandlerArgs = [
                $errorRender['class'],
                $errorRender['method']
            ];
        }

        set_error_handler($errorHandlerArgs);
    }
    
    protected function defineExceptionHandler()
    {
        $calledClass     = get_called_class();
        $exceptionRender = $calledClass::getExceptionRender();
        
        if ($exceptionRender === false) {
            return;
        }
        
        $erxceptionHandlerArgs = $exceptionRender['method'];
        if (!empty($exceptionRender['class'])) {
            $erxceptionHandlerArgs = [
                $exceptionRender['class'],
                $exceptionRender['method']
            ];
        }

        set_exception_handler($erxceptionHandlerArgs);
    }
    
    protected static function getApp()
    {
        if(is_null(self::$app)) {
            self::$app = Application::getInstance();
        }
        
        return self::$app;
    }
    
    public static function getErrorRender()
    {
        $calledClass = get_called_class();
        $app         = $calledClass::getApp();
        $renderFcts  = $app->getConfig('errorRenderFct');
        
        return self::defineRenderToUse($renderFcts);
    }
    
    public static function getExceptionRender()
    {
        $calledClass = get_called_class();
        $app         = $calledClass::getApp();
        $renderFcts  = $app->getConfig('exceptionRenderFct');
        
        return self::defineRenderToUse($renderFcts);
    }
    
    protected static function defineRenderToUse($renderConfig)
    {
        if($renderConfig['active'] === false) {
            return false;
        }
        
        if (PHP_SAPI === 'cli' && isset($renderConfig['cli'])) {
            return $renderConfig['cli'];
        }
        
        if(isset($renderConfig['default'])) {
            return $renderConfig['default'];
        }
        
        return false;
    }
    
    public static function exceptionHandler($exception)
    {
        $calledClass = get_called_class();
        $errorRender = $calledClass::getExceptionRender();
        
        $calledClass::callRender(
            $errorRender,
            'Fatal', 
            $exception->getMessage(), 
            $exception->getFile(), 
            $exception->getLine(), 
            $exception->getTrace()
        );
    }
    
    public static function errorHandler(
        $errSeverity,
        $errMsg,
        $errFile,
        $errLine
    ) {
        $calledClass = get_called_class();
        $erreurType  = $calledClass::getErrorType($errSeverity);
        $errorRender = $calledClass::getErrorRender();
        
        $calledClass::callRender(
            $errorRender,
            $erreurType,
            $errMsg,
            $errFile,
            $errLine,
            debug_backtrace()
        );
    }
    
    protected static function callRender(
        $renderInfos,
        $erreurType,
        $errMsg,
        $errFile,
        $errLine,
        $backtrace
    ) {
        $class  = $renderInfos['class'];
        $method = $renderInfos['method'];
        
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
        
        $method(
            $erreurType,
            $errMsg,
            $errFile,
            $errLine,
            $backtrace
        );
    }
    
    protected static function getErrorType($errSeverity)
    {
        //List : http://fr2.php.net/manual/fr/function.set-error-handler.php#113567
        $map = [
            E_ERROR             => 'Fatal',
            E_CORE_ERROR        => 'Fatal',
            E_USER_ERROR        => 'Fatal',
            E_COMPILE_ERROR     => 'Fatal',
            E_RECOVERABLE_ERROR => 'Fatal',
            E_WARNING           => 'Fatal',
            E_CORE_WARNING      => 'Fatal',
            E_USER_WARNING      => 'Fatal',
            E_COMPILE_WARNING   => 'Fatal',
            E_PARSE             => 'Parse',
            E_NOTICE            => 'Notice',
            E_USER_NOTICE       => 'Notice',
            E_STRICT            => 'Strict',
            E_RECOVERABLE_ERROR => '/',
            E_DEPRECATED        => 'Deprecated',
            E_USER_DEPRECATED   => 'Deprecated'
        ];

        $erreurType = 'Unknown';
        if (isset($map[$errSeverity])) {
            $erreurType = $map[$errSeverity];
        }
        
        return $erreurType;
    }

    public static function defaultCliErrorRender(
        $erreurType,
        $errMsg,
        $errFile,
        $errLine,
        $backtrace
    ) {
        $msgError = $erreurType.' Error : '.$errMsg.
            ' in '.$errFile.' at line '.$errLine;
        
        \BFW\Cli\displayMsg(
            $msgError,
            'white',
            'red'
        );
    }

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
                <title>Une erreur est parmi nous !</title>
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

<?php

namespace BFW\Core;

use \BFW\Application;

class Errors
{
    public function __construct()
    {
        set_exception_handler(['\BFW\Core\Errors', 'exceptionHandler']);
        set_error_handler(['\BFW\Core\Errors', 'errorHandler']);
    }
    
    public static function errorGetRender()
    {
        $app = Application::getInstance();
        return $app->getConfig('errorRenderFct');
    }
    
    public static function exceptionHandler($exception)
    {
        //trigger_error($exception->getMessage(), E_USER_WARNING);
        $errorRender = self::errorGetRender();
        $errorRender(
            'Fatal', 
            $exception->getMessage(), 
            $exception->getFile(), 
            $exception->getLine(), 
            $exception->getTrace()
        );
    }
    
    public static function errorHandler($errSeverity, $errMsg, $errFile, $errLine)
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
        if(isset($map[$errSeverity])) {
            $erreurType = $map[$errSeverity];
        }
        
        $errorRenderFcts = self::errorGetRender();
        $errorRender      = $errorRenderFcts['default'];
        
        if(PHP_SAPI === 'cli') {
            $errorRender = $errorRenderFcts['cli'];
        }
        
        $errorRender($erreurType, $errMsg, $errFile, $errLine, debug_backtrace());
    }
    
    public static function defaultCliErrorRender($erreurType, $errMsg, $errFile, $errLine, $backtrace)
    {
        \BFW\Cli\displayMsg($erreurType.' Error : <strong>'.$errMsg.'</strong> in '.$errFile.' at line '.$errLine, 'white', 'red');
    }
    
    public static function defaultErrorRender($erreurType, $errMsg, $errFile, $errLine, $backtrace)
    {
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
                        foreach($backtrace as $i => $info) {
                            echo '#'.$i.'  '.$info['function'];
                            
                            if(isset($info['args']) && count($info['args']) > 0) {
                                echo '(';
                                
                                foreach($info['args'] as $iArgs => $args) {
                                    if($iArgs > 0) {
                                        echo ', ';
                                    }
                                    
                                    if(is_array($args) || is_object($args)) {
                                        echo gettype($args);
                                        
                                    }
                                    elseif(is_null($args)) {
                                        echo 'null';
                                    }
                                    else {
                                        echo htmlentities($args);
                                    }
                                }
                                
                                echo ')';
                            }
                            
                            if(isset($info['file'], $info['line'])) {
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

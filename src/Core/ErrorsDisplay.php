<?php

namespace BFW\Core;

class ErrorsDisplay
{
    /**
     * The default cli render in BFW
     * 
     * @param string   $errType : Human readable error severity
     * @param string   $errMsg : Error/exception message
     * @param string   $errFile : File where the error/exception is triggered
     * @param integer  $errLine : Line where the error/exception is triggered
     * @param array    $backtrace : Error/exception backtrace
     * @param int|null $exceptionCode : Exception code
     * 
     * @return void
     */
    public static function defaultCliErrorRender(
        string $errType,
        string $errMsg,
        string $errFile,
        int $errLine,
        array $backtrace,
        $exceptionCode
    ) {
        if (!empty($exceptionCode)) {
            $errMsg = '#'.$exceptionCode.' : '.$errMsg;
        }
        
        //Create the cli message
        $msgError = $errType.' Error : '.$errMsg.
            ' in '.$errFile.' at line '.$errLine;
        
        echo "\033[0;37;41m".$msgError."\033[0m\n";
        ob_flush();
        exit;
    }

    /**
     * The default error render in BFW
     * 
     * @param string   $errType : Human readable error severity
     * @param string   $errMsg : Error/exception message
     * @param string   $errFile : File where the error/exception is triggered
     * @param integer  $errLine : Line where the error/exception is triggered
     * @param array    $backtrace : Error/exception backtrace
     * @param int|null $exceptionCode : Exception code
     * 
     * @return void
     */
    public static function defaultErrorRender(
        string $errType,
        string $errMsg,
        string $errFile,
        int $errLine,
        array $backtrace,
        $exceptionCode
    ) {
        http_response_code(500);
        ob_clean();

        if (!empty($exceptionCode)) {
            $errMsg = '#'.$exceptionCode.' : '.$errMsg;
        }
        
        echo '
        <!doctype html>
        <html lang="fr">
            <head>
                <title>An error is detected !</title>
                <style>
                    html {padding:0; margin:0; background-color:#e3e3e3; font-family:sans-serif; font-size: 1em; word-wrap:break-word;}
                    div {position:relative; margin:auto; width:950px; border: 1px solid #a6c9e2; top: 30px; margin-bottom:10px;}
                    p {padding:0; margin:0;}
                    p.title {font-size:1.2em; background-color:#D0DCE9; padding:10px;}
                    p.info {padding:5px; margin-top:10px; margin-bottom:10px;}
                    fieldset {border:none; background-color: white;}
                    pre {width:910px; line-height:1.5; white-space:pre-line;}
                </style>
            </head>
            <body>
                <div>
                    <p class="title">Niarf, an error is detected !</p>
                    <p class="info">'.$errType.' Error : <strong>'.$errMsg.'</strong> in '.$errFile.' at line '.$errLine.'</p>
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

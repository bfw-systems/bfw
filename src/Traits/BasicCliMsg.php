<?php

namespace BFW\Traits;

trait BasicCliMsg
{
    protected function displayMsgInCli(
        string $msg,
        string $txtColor='white',
        string $txtStyle='normal'
    ) {
        $nbArgs = func_num_args();
        if ($nbArgs === 1) {
            echo $msg;
            ob_flush();
            return;
        }
        
        $txtStyleCode = $this->obtainShellTxtStyleCode($txtStyle);
        $txtColorCode = $this->obtainShellTxtColorCode($txtColor);
        
        echo "\033[".$txtStyleCode.";".$txtColorCode."m".$msg."\033[0m";
        ob_flush();
    }
    
    protected function displayMsgNLInCli(
        string $msg,
        string $txtColor='white',
        string $txtStyle='normal'
    ) {
        $nbArgs = func_num_args();
        if ($nbArgs === 1) {
            $this->displayMsgInCli($msg."\n");
            return;
        }
        
        $this->displayMsgInCli($msg."\n", $txtColor, $txtStyle);
    }
    
    protected function obtainShellTxtColorCode(string $txtColor): int
    {
        if ($txtColor === 'red') {
            return 31;
        } elseif ($txtColor === 'green') {
            return 32;
        } elseif ($txtColor === 'yellow') {
            return 33;
        }
        
        return 37; //white
    }
    
    protected function obtainShellTxtStyleCode(string $txtStyle): int
    {
        if ($txtStyle === 'bold') {
            return 1;
        }
        
        return 0; //normal
    }
}

<?php

/**
 * Handler functions for Errors unit test
 */

function default_error_render()
{
    //Do nothing.
}

function cli_error_render()
{
    //Do nothing.
}

function default_exception_render()
{
    //Do nothing.
}

function cli_exception_render()
{
    //Do nothing.
}

$fctLastRenderCallInfos = new \stdClass;
function fctErrorRender(
    $erreurType,
    $errMsg,
    $errFile,
    $errLine,
    $backtrace
) {
    global $fctLastRenderCallInfos;
    
    $fctLastRenderCallInfos = (object) [
        'erreurType' => $erreurType,
        'errMsg' => $errMsg,
        'errFile' => $errFile,
        'errLine' => $errLine,
        'backtrace' => $backtrace
    ];
}

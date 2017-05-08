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
    $errType,
    $errMsg,
    $errFile,
    $errLine,
    $backtrace
) {
    global $fctLastRenderCallInfos;
    
    $fctLastRenderCallInfos = (object) [
        'errType'   => $errType,
        'errMsg'    => $errMsg,
        'errFile'   => $errFile,
        'errLine'   => $errLine,
        'backtrace' => $backtrace
    ];
}

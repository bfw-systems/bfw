<?php

namespace BFW\Test\Helpers;

/**
 * Function used by \Core\Errors unit test to test with a personal render fct
 * 
 * @param string $errType
 * @param string $errMsg
 * @param string $errFile
 * @param string $errLine
 * @param array $backtrace
 * 
 * @return void
 */
function errorsRenderFunction(
    $errType,
    $errMsg,
    $errFile,
    $errLine,
    $backtrace
) {
    ErrorsRenderClass::render(
        $errType,
        $errMsg,
        $errFile,
        $errLine,
        $backtrace
    );
}

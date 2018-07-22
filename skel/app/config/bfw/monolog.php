<?php
/**
 * Config file for monolog
 * 
 * @author bulton-fr <bulton.fr@gmail.com>
 * @version 3.0.0
 * @package bfw
 */

use Monolog\Logger;

return (object) [
    'handlers' => [
        //1.x Monolog always send to stdout if no handler is define :/
        (object) [
            'name' => '\Monolog\Handler\TestHandler',
            'args' => []
        ]
        /**
         * Value example:
        (object) [
            'name' => '\Monolog\Handler\StreamHandler',
            'args' => [
                APP_DIR.'logs/bfw/bfw.log',
                Logger::DEBUG
            ]
        ]
        */
    ]
];

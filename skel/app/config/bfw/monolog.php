<?php
/**
 * Config file for monolog
 * 
 * @author bulton-fr <bulton.fr@gmail.com>
 * @version 3.0.0
 * @package bfw
 */

use Monolog\Logger;

return [
    'handlers' => [
        //1.x Monolog always send to stdout if no handler is define :/
        [
            'name' => '\Monolog\Handler\TestHandler',
            'args' => []
        ]
        /**
         * Value example:
        [
            'name' => '\Monolog\Handler\StreamHandler',
            'args' => [
                APP_DIR.'logs/bfw/bfw.log',
                Logger::DEBUG
            ]
        ]
        */
    ]
];

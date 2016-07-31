<?php
/**
 * BFW Framework config file
 * 
 * @author bulton-fr <bulton.fr@gmail.com>
 * @version 3.0.0
 * @package bfw
 */

return [
    'debug' => false,
    'errorRenderFct' => [
        'default' => '\BFW\Core\Errors::defaultErrorRender',
        'cli'     => '\BFW\Core\Errors::defaultCliErrorRender'
    ],
    'sqlSecureMethod' => '',
    'memcached' => [
        'enabled' => false,
        'class'   => '\BFW\Memcache\Memcached',
        'server'  => [
            [
                'host' => '',
                'port' => ''
            ]
        ]
    ],
    'modules' => [
        'db' => [
            'name'    => '',
            'enabled' => false
        ],
        'controller' => [
            'name'    => '',
            'enabled' => false
        ],
        'routing' => [
            'name'    => '',
            'enabled' => false
        ],
        'template' => [
            'name'   => '',
            'enabled'=> false
        ]
    ]
];

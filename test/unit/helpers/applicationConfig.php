<?php
/**
 * BFW mocked config file
 */

return [
    'debug'              => false,
    'errorRenderFct'     => [
        'enabled' => false,
        'default' => [
            'class'  => '',
            'method' => ''
        ],
        'cli'     => [
            'class'  => '',
            'method' => ''
        ]
    ],
    'exceptionRenderFct' => [
        'enabled' => false,
        'default' => [
            'class'  => '',
            'method' => ''
        ],
        'cli'     => [
            'class'  => '',
            'method' => ''
        ]
    ],
    'sqlSecureMethod' => '',
    'memcached'          => [
        'enabled'      => false,
        'class'        => '',
        'persistentId' => null,
        'servers'      => []
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
        'router' => [
            'name'    => '',
            'enabled' => false
        ],
        'template' => [
            'name'   => '',
            'enabled'=> false
        ]
    ]
];

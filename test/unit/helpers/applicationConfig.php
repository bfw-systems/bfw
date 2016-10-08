<?php

return [
    'debug'              => false,
    'errorRenderFct'     => [
        'active'  => false,
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
        'active'  => false,
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
        'server'       => []
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

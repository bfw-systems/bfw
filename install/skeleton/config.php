<?php
/**
 * BFW Framework config file
 * 
 * @author bulton-fr <bulton.fr@gmail.com>
 * @version 3.0.0
 * @package bfw
 */

return [
    /**
     * @var boolean debug : Debug mode
     */
    'debug'              => false,
    
    /**
     * @var array errorRenderFct : Function use to display a personnal
     *  page/message for an php error
     */
    'errorRenderFct'     => [
        /**
         * @var boolean enabled : To enable the error render
         */
        'enabled'  => false,
        /**
         * @var array default : For the no cli mode
         */
        'default' => [
            /**
             * @var string class : Class where is the render error function.
             * Empty if it's not a class
             */
            'class'  => '\BFW\Core\Errors',
            /**
             * @var string method : Method or function for the render error.
             */
            'method' => 'defaultErrorRender'
        ],
        /**
         * @var array cli : For the cli mode
         */
        'cli'     => [
            /**
             * @var string class : Class where is the render error function.
             * Empty if it's not a class
             */
            'class'  => '\BFW\Core\Errors',
            /**
             * @var string method : Method or function for the render error.
             */
            'method' => 'defaultCliErrorRender'
        ]
    ],
    
    /**
     * @var array exceptionRenderFct : Function use to display a personnal
     *  page/message for an exception
     */
    'exceptionRenderFct' => [
        /**
         * @var boolean enabled : To enable the exception render
         */
        'enabled'  => false,
        /**
         * @var array default : For the no cli mode
         */
        'default' => [
            /**
             * @var string class : Class where is the render exception function
             * Empty if it's not a class
             */
            'class'  => '\BFW\Core\Errors',
            /**
             * @var string method : Method or function for the render exception
             */
            'method' => 'defaultErrorRender'
        ],
        /**
         * @var array cli : For the cli mode
         */
        'cli'     => [
            /**
             * @var string class : Class where is the render exception function
             * Empty if it's not a class
             */
            'class'  => '\BFW\Core\Errors',
            /**
             * @var string method : Method or function for the render exception
             */
            'method' => 'defaultCliErrorRender'
        ]
    ],
    
    /**
     * @var null|callable sqlSecureMethod : SQL Method used for securise datas
     */
    'sqlSecureMethod'    => null,
    
    /**
     * @var array memcached : Memcache(d) server(s) config
     */
    'memcached'          => [
        /**
         * @var boolean enabled : Enable or not connection to memcache(d)
         */
        'enabled'      => false,
        
        /**
         * @var string class : Class to use for connect to server
         * you can use other class than BFW
         * BFW class :
         * * Memcached : \BFW\Memcache\Memcached
         * * Memcache : \BFW\Memcache\Memcache
         */
        'class'        => '\BFW\Memcache\Memcached',
        
        /**
         * @var string|null persistantId : Unique ID for the instance.
         * To create an instance that persists between requests
         * 
         * For memcached only.
         * 
         * @see http://php.net/manual/en/memcached.construct.php
         */
        'persistentId' => null,
        
        /**
         * @var array servers : List of memcache(d) server(s) to connect.
         */
        'servers'      => [
            /**
             * First server to connect.
             * Duplicate this array to connect at others servers.
             */
            [
                /**
                 * @var string host : Memcache(d) host
                 */
                'host'       => '',
                
                /**
                 * @var int post : Memcache(d) port
                 */
                'port'       => 0,
                
                /**
                 * @var int weight : The weight of the server relative to the
                 *  total weight of all the servers in the pool.
                 * 
                 * @see http://php.net/manual/en/memcached.addserver.php
                 */
                'weight'     => 0,
                
                /**
                 * @var int|null : Value in seconds which will be used for
                 *  connecting to the daemon.
                 * 
                 * For memcache only
                 * It's recommended to stay value at null.
                 * 
                 * @see http://php.net/manual/fr/memcache.connect.php
                 */
                'timeout'    => null,
                
                /**
                 * @var boolean persistent : If the connection should be
                 *  persistent
                 * 
                 * For memcache only
                 */
                'persistent' => false
            ]
        ]
    ],
    
    /**
     * @var array modules : To declare and enable a core module
     */
    'modules'            => [
        
        /**
         * @var array db : The module who create the link with the database
         */
        'db'         => [
            /**
             * @var string name : Module's name
             */
            'name'    => '',
            /**
             * @var boolean enabled : If the module is enable or not
             */
            'enabled' => false
        ],
        
        /**
         * @var array controller : The module who manage input request
         */
        'controller' => [
            /**
             * @var string name : Module's name
             */
            'name'    => '',
            /**
             * @var boolean enabled : If the module is enable or not
             */
            'enabled' => false
        ],
        
        /**
         * @var array router : The module who map url to a controller
         */
        'router'     => [
            /**
             * @var string name : Module's name
             */
            'name'    => '',
            /**
             * @var boolean enabled : If the module is enable or not
             */
            'enabled' => false
        ],
        
        /**
         * @var array template : The module used for templating
         */
        'template'   => [
            /**
             * @var string name : Module's name
             */
            'name'    => '',
            /**
             * @var boolean enabled : If the module is enable or not
             */
            'enabled' => false
        ]
    ]
];

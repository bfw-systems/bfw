<?php
/**
 * Config file for memcache(d) system
 * 
 * @author bulton-fr <bulton.fr@gmail.com>
 * @version 3.0.0
 * @package bfw
 */

return [
    /**
     * @var array memcached : Memcache(d) server(s) config
     */
    'memcached' => [
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
    ]
];

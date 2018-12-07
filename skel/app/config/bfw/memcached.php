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
                'host'   => '',
                
                /**
                 * @var int post : Memcache(d) port
                 */
                'port'   => 0,
                
                /**
                 * @var int weight : The weight of the server relative to the
                 *  total weight of all the servers in the pool.
                 * 
                 * @see http://php.net/manual/en/memcached.addserver.php
                 */
                'weight' => 0
            ]
        ]
    ]
];

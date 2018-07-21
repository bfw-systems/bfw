<?php
/**
 * Config file for modules to use
 * 
 * @author bulton-fr <bulton.fr@gmail.com>
 * @version 3.0.0
 * @package bfw
 */

return [
    /**
     * @var array modules : To declare and enable a core module
     */
    'modules' => [
        
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

<?php
/**
 * Config file for errors system
 * 
 * @author bulton-fr <bulton.fr@gmail.com>
 * @version 3.0.0
 * @package bfw
 */

return [
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
            'class'  => '\BFW\Core\ErrorsDisplay',
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
            'class'  => '\BFW\Core\ErrorsDisplay',
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
            'class'  => '\BFW\Core\ErrorsDisplay',
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
            'class'  => '\BFW\Core\ErrorsDisplay',
            /**
             * @var string method : Method or function for the render exception
             */
            'method' => 'defaultCliErrorRender'
        ]
    ]
];

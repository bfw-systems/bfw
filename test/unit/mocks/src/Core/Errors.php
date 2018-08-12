<?php

namespace BFW\Test\Mock\Core;

/**
 * Define the Error class without the handler redefine.
 * Used by Application mock.
 */
Class Errors extends \BFW\Core\Errors
{
    /**
     * {@inheritdoc}
     * Disable handlers.
     */
    public function __construct()
    {
        //Disable parent construct.
    }
}

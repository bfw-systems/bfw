<?php

namespace BFW\Test\Helpers;

/**
 * A method used by unit test to have a function to declare into config value
 * for sqlSecureMethod.
 * There are no secure into this function. It's only return the value passed
 * into parameters.
 * 
 * @param string $value
 * 
 * @return string
 */
function secureSqlMethod($value)
{
    return $value;
}

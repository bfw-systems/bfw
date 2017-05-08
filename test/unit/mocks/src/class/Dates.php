<?php

namespace BFW\test\unit\mocks;

/**
 * Mock for Dates class
 */
class Dates extends \BFW\Dates
{
    /**
     * Magic getter
     * 
     * @link http://php.net/manual/en/language.oop5.overloading.php#object.get
     * 
     * @param string $propertyName The property name
     * 
     * @return mixed
     */
    public function __get($propertyName)
    {
        return $this->{$propertyName};
    }
}

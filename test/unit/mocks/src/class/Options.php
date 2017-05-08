<?php

namespace BFW\test\unit\mocks;

/**
 * Mock for Options class
 */
class Options extends \BFW\Options
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

<?php

namespace BFW\test\unit\mocks;

class Dates extends \BFW\Dates
{
    /**
     * Accesseur get
     */
    public function __get($name)
    {
        return $this->$name;
    }
}

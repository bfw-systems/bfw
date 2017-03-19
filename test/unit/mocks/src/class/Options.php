<?php

namespace BFW\test\unit\mocks;

class Options extends \BFW\Options
{
    /**
     * Accesseur get
     */
    public function __get($name)
    {
        return $this->$name;
    }
}

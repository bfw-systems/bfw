<?php

namespace BFW\Test\Mock\Core\AppSystems;

//To be included by module who use it
require_once(__DIR__.'/../Errors.php');

class Errors extends \BFW\Core\AppSystems\Errors
{
    public function init()
    {
        $this->errors     = new \BFW\Test\Mock\Core\Errors;
        $this->initStatus = true;
    }
}

<?php

namespace BFW\Core\AppSystems\Test\Mock;

//To be included by module who use it
require_once(__DIR__.'/../Errors.php');

class Errors extends \BFW\Core\AppSystems\Errors
{
    public function init()
    {
        $this->errors     = new \BFW\Core\Test\Mock\Errors;
        $this->initStatus = true;
    }
}

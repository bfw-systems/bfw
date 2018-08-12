<?php

namespace BFW\Core\AppSystems\Test\Mock;

//To be included by module who use it
require_once(__DIR__.'/../Cli.php');

class Cli extends \BFW\Core\AppSystems\Cli
{
    public function init()
    {
        $this->cli        = new \BFW\Core\Test\Mock\Cli;
        $this->initStatus = true;
    }
}

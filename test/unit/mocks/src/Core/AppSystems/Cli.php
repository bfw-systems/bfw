<?php

namespace BFW\Test\Mock\Core\AppSystems;

//To be included by module who use it
require_once(__DIR__.'/../Cli.php');

class Cli extends \BFW\Core\AppSystems\Cli
{
    public function init()
    {
        $this->cli        = new \BFW\Test\Mock\Core\Cli;
        $this->initStatus = true;
    }
}

<?php

namespace BFW\test\unit\mocks;

//To have the config mock loaded for external module which use this class.
require_once(__DIR__.'/../../../helpers/Application.php');

class Application extends \BFW\Application
{
    use \BFW\test\helpers\Application;
}

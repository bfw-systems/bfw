<?php

namespace BFW\Helpers\test\unit\mocks;

class Secure extends \BFW\Helpers\Secure
{
    protected static function getApplicationInstance()
    {
        parent::getApplicationInstance(); //For code coverage.
        
        return \BFW\test\unit\mocks\Application::getInstance();
    }
}

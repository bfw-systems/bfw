<?php

namespace BFW\test\unit\mocks;

class MockModuleRunnerFile extends \BFW\Module
{
    public function callGetRunnerFile()
    {
        return parent::getRunnerFile();
    }
}

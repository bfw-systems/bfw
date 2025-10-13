<?php

namespace BFW\test\unit;

use atoum;

require_once(__DIR__ . '/../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class ModuleInterface extends atoum
{
    public function testInterface()
    {
        $this->assert('test ModuleInterface exists and has required methods')
            ->class('BFW\ModuleInterface')
                ->hasInterface('BFW\ModuleInterface')
            ->class('BFW\ModuleInterface')
                ->hasMethod('run')
        ;
    }
}
<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Constants extends atoum
{
    public function testCreate()
    {
        $this->assert('test Helpers\Constants::create with new constant')
            ->variable(\BFW\Helpers\Constants::create('BFW_LIB_UNIT_TEST', 'atoum'))
                ->isNull()
            ->string(constant('BFW_LIB_UNIT_TEST'))
                ->isEqualTo('atoum')
        ;
        
        $this->assert('test Helpers\Constants::create with existing constant')
            ->exception(function() {
                \BFW\Helpers\Constants::create('BFW_LIB_UNIT_TEST', 'atoum');
            })
                ->hasCode(\BFW\Helpers\Constants::ERR_ALREADY_DEFINED)
        ;
    }
}

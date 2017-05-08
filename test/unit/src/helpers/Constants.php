<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Constants extends atoum
{
    /**
     * Test method for create()
     * 
     * @return void
     */
    public function testCreate()
    {
        $this->assert('test Constants::create with not existing constant')
            ->if(\BFW\Helpers\Constants::create('TEST', 'test constant'))
            ->then
            ->string(TEST)
                ->isEqualTo('test constant');
        
        $this->assert('test Constants::create with existing constant')
            ->exception(function() {
                \BFW\Helpers\Constants::create('TEST', 'test constant exist');
            })
                ->hasMessage('The constant TEST is already defined.');
    }
}

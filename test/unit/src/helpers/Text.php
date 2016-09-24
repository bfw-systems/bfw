<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class String extends atoum
{
    public function testNl2br()
    {
        $this->assert('test String::nl2br')
            ->string(\BFW\Helpers\String::nl2br('unit'."\r\n".'test'))
                ->isEqualTo('unit<br>test')
            ->string(\BFW\Helpers\String::nl2br('unit'."\n\r".'test'))
                ->isEqualTo('unit<br>test')
            ->string(\BFW\Helpers\String::nl2br('unit'."\r".'test'))
                ->isEqualTo('unit<br>test')
            ->string(\BFW\Helpers\String::nl2br('unit'."\n".'test'))
                ->isEqualTo('unit<br>test');
    }
}

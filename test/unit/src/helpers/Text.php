<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Text extends atoum
{
    /**
     * Test method for nl2br
     * 
     * @return void
     */
    public function testNl2br()
    {
        $this->assert('test Text::nl2br')
            ->string(\BFW\Helpers\Text::nl2br('unit'."\r\n".'test'))
                ->isEqualTo('unit<br>test')
            ->string(\BFW\Helpers\Text::nl2br('unit'."\n\r".'test'))
                ->isEqualTo('unit<br>test')
            ->string(\BFW\Helpers\Text::nl2br('unit'."\r".'test'))
                ->isEqualTo('unit<br>test')
            ->string(\BFW\Helpers\Text::nl2br('unit'."\n".'test'))
                ->isEqualTo('unit<br>test');
    }
}

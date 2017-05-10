<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Http extends atoum
{
    /**
     * Test method for redirect()
     * Get header Location is not possible from cli.
     * The case with call to exit is not testable.
     * 
     * @return void
     */
    public function testRedirect()
    {
        $this->assert('test Http::redirect with not permanent redirect')
            ->if(\BFW\Helpers\Http::redirect('test.php'))
            ->then
            ->integer(http_response_code())
                ->isEqualTo(302);
        
        $this->assert('test Http::redirect with indicated not permanent redirect')
            ->if(\BFW\Helpers\Http::redirect('test.php', false))
            ->then
            ->integer(http_response_code())
                ->isEqualTo(302);
        
        $this->assert('test Http::redirect with a permanent redirect')
            ->if(\BFW\Helpers\Http::redirect('test.php', true))
            ->then
            ->integer(http_response_code())
                ->isEqualTo(301);
    }
}

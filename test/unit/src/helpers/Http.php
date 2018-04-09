<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Http extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    public function beforeTestMethod($testMethod)
    {
        $this->createApp();
        $this->initApp();
    }
    
    public function testRedirect()
    {
        $this->assert('test Helpers\Http::redirect with not permanent redirect')
            ->if($this->function->http_response_code = null)
            ->and($this->function->header = null)
            ->then
            
            ->variable(\BFW\Helpers\Http::redirect('/atoum.php'))
                ->isNull()
            ->function('http_response_code')
                ->wasCalledWithArguments(302)
                    ->atLeastOnce()
            ->function('header')
                ->wasCalledWithArguments('Location: /atoum.php')
                    ->atLeastOnce()
        ;
        
        $this->assert('test Helpers\Http::redirect with a permanent redirect')
            ->if($this->function->http_response_code = null)
            ->and($this->function->header = null)
            ->then
            
            ->variable(\BFW\Helpers\Http::redirect('/atoum.php', true))
                ->isNull()
            ->function('http_response_code')
                ->wasCalledWithArguments(301)
                    ->atLeastOnce()
            ->function('header')
                ->wasCalledWithArguments('Location: /atoum.php')
                    ->atLeastOnce()
        ;
        
        //Can not test the call to exit keyword because he exit atoum too
    }
    
    public function testObtainPostKey()
    {
        //We can not mock anything into :/
        //So we test only the return and not the args passed to called method inside
        
        $this->assert('test Helpers\Http::obtainPostKey')
            ->given($_POST = [
                'id'      => 42,
                'titre'   => 'install',
                'content' => '<p>Il est recommandé d\'utiliser composer pour installer</p>',
            ])
            ->string(\BFW\Helpers\Http::obtainPostKey('content', 'string', true))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d\\\'utiliser composer pour installer&lt;/p&gt;')
        ;
    }
    
    public function testObtainGetKey()
    {
        //We can not mock anything into :/
        //So we test only the return and not the args passed to called method inside
        
        $this->assert('test Helpers\Http::obtainGetKey')
            ->given($_GET = [
                'id'      => 42,
                'titre'   => 'install',
                'content' => '<p>Il est recommandé d\'utiliser composer pour installer</p>',
            ])
            ->string(\BFW\Helpers\Http::obtainGetKey('content', 'string', true))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d\\\'utiliser composer pour installer&lt;/p&gt;')
        ;
    }
}

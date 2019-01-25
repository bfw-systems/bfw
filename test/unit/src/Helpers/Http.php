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
        $this->setRootDir(__DIR__.'/../../../..');
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
        
        $this->assert('test Helpers\Http::obtainPostKey - prepare')
            ->given($_POST = [
                'id'      => 42,
                'titre'   => 'install',
                'content' => " \n\t\n".' <p>Il est recommandé d\'utiliser composer pour installer</p>',
            ])
        ;
        
        $this->assert('test Helpers\Http::obtainPostKey - not html, but inline (default)')
            ->string(\BFW\Helpers\Http::obtainPostKey('content', 'string', true))
                ->isEqualTo('Il est recommand&eacute; d&#039;utiliser composer pour installer')
        ;
        
        $this->assert('test Helpers\Http::obtainPostKey - html, and inline (default)')
            ->string(\BFW\Helpers\Http::obtainPostKey('content', 'html', true))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;')
        ;
        
        $this->assert('test Helpers\Http::obtainPostKey - html, but not inline')
            ->string(\BFW\Helpers\Http::obtainPostKey('content', 'html', true, false))
                ->isEqualTo("\n\t\n".' &lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;')
        ;
    }
    
    public function testObtainGetKey()
    {
        //We can not mock anything into :/
        //So we test only the return and not the args passed to called method inside
        
        $this->assert('test Helpers\Http::obtainGetKey - prepare')
            ->given($_GET = [
                'id'      => 42,
                'titre'   => 'install',
                'content' => " \n\t\n".' <p>Il est recommandé d\'utiliser composer pour installer</p>',
            ])
        ;
        
        $this->assert('test Helpers\Http::obtainGetKey - not html, but inline (default)')
            ->string(\BFW\Helpers\Http::obtainGetKey('content', 'string', true))
                ->isEqualTo('Il est recommand&eacute; d&#039;utiliser composer pour installer')
        ;
        
        $this->assert('test Helpers\Http::obtainGetKey - html, and inline (default)')
            ->string(\BFW\Helpers\Http::obtainGetKey('content', 'html', true))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;')
        ;
        
        $this->assert('test Helpers\Http::obtainGetKey - html, but not inline')
            ->string(\BFW\Helpers\Http::obtainGetKey('content', 'html', true, false))
                ->isEqualTo("\n\t\n".' &lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;')
        ;
    }
    
    public function testObtainManyPostKey()
    {
        //We can not mock anything into :/
        //So we test only the return and not the args passed to called method inside
        
        $this->assert('test Helpers\Http::obtainManyPostKeys - prepare')
            ->given($_POST = [
                'id'      => 42,
                'titre'   => 'install',
                'content' => " \n\t\n".' <p>Il est recommandé d\'utiliser composer pour installer</p>',
            ])
        ;
        
        $this->assert('test Helpers\Http::obtainManyPostKeys - not html, but inline (default)')
            ->array(\BFW\Helpers\Http::obtainManyPostKeys(
                [
                    'titre' => 'string',
                    'content' => [
                        'type'         => 'string',
                        'htmlentities' => true,
                        'inline'       => true
                    ]
                ]
            ))
                ->isEqualTo([
                    'titre'   => 'install',
                    'content' => 'Il est recommand&eacute; d&#039;utiliser composer pour installer'
                ])
        ;
        
        $this->assert('test Helpers\Http::obtainManyPostKeys - html, and inline (default)')
            ->array(\BFW\Helpers\Http::obtainManyPostKeys(
                [
                    'titre' => 'string',
                    'content' => [
                        'type'         => 'html',
                        'htmlentities' => true,
                        'inline'       => true
                    ]
                ]
            ))
                ->isEqualTo([
                    'titre'   => 'install',
                    'content' => '&lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;'
                ])
        ;
        
        $this->assert('test Helpers\Http::obtainManyPostKeys - html, but not inline')
            ->array(\BFW\Helpers\Http::obtainManyPostKeys(
                [
                    'titre' => 'string',
                    'content' => [
                        'type'         => 'html',
                        'htmlentities' => true,
                        'inline'       => false
                    ]
                ]
            ))
                ->isEqualTo([
                    'titre'   => 'install',
                    'content' => "\n\t\n".' &lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;'
                ])
        ;
    }
    
    public function testObtainManyGetKey()
    {
        //We can not mock anything into :/
        //So we test only the return and not the args passed to called method inside
        
        $this->assert('test Helpers\Http::obtainManyGetKeys - prepare')
            ->given($_GET = [
                'id'      => 42,
                'titre'   => 'install',
                'content' => " \n\t\n".' <p>Il est recommandé d\'utiliser composer pour installer</p>',
            ])
        ;
        
        $this->assert('test Helpers\Http::obtainManyGetKeys - not html, but inline (default)')
            ->array(\BFW\Helpers\Http::obtainManyGetKeys(
                [
                    'titre' => 'string',
                    'content' => [
                        'type'         => 'string',
                        'htmlentities' => true,
                        'inline'       => true
                    ]
                ]
            ))
                ->isEqualTo([
                    'titre'   => 'install',
                    'content' => 'Il est recommand&eacute; d&#039;utiliser composer pour installer'
                ])
        ;
        
        $this->assert('test Helpers\Http::obtainManyGetKeys - html, and inline (default)')
            ->array(\BFW\Helpers\Http::obtainManyGetKeys(
                [
                    'titre' => 'string',
                    'content' => [
                        'type'         => 'html',
                        'htmlentities' => true,
                        'inline'       => true
                    ]
                ]
            ))
                ->isEqualTo([
                    'titre'   => 'install',
                    'content' => '&lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;'
                ])
        ;
        
        $this->assert('test Helpers\Http::obtainManyGetKeys - html, but not inline')
            ->array(\BFW\Helpers\Http::obtainManyGetKeys(
                [
                    'titre' => 'string',
                    'content' => [
                        'type'         => 'html',
                        'htmlentities' => true,
                        'inline'       => false
                    ]
                ]
            ))
                ->isEqualTo([
                    'titre'   => 'install',
                    'content' => "\n\t\n".' &lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;'
                ])
        ;
    }
}

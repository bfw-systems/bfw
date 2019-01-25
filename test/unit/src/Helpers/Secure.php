<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');
require_once(__DIR__.'/../../helpers/SecureSqlMethodFct.php');

/**
 * @engine isolate
 */
class Secure extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../..');
        $this->createApp();
        $this->initApp();
    }
    
    public function testHash()
    {
        $this->assert('test Helpers\Secure::hash')
            ->string(\BFW\Helpers\Secure::hash('atoum'))
                ->isEqualTo(hash('sha256', md5('atoum')))
        ;
    }
    
    protected function assertSecureKnownType(
        &$filterType,
        $dataToTest,
        $filter,
        $expectedData,
        $expectedFilter
    ) {
        $this->assert('test Helpers\Secure::secureKnownType with '.$filter.' types')
            ->given($filterType = null)
            ->variable(\BFW\Helpers\Secure::secureKnownType($dataToTest, $filter))
                ->isIdenticalTo($expectedData)
            ->variable($filterType)
                ->isIdenticalTo($expectedFilter)
        ;
    }
    
    public function testSecureKnownType()
    {
        $this->assert('test Helpers\Secure::secureKnownType - prepare')
            ->given($filterType = null)
            ->given($this->function->filter_var = function($variable, $filter) use (&$filterType) {
                $filterType = $filter;
                
                return \filter_var($variable, $filter);
            })
        ;
        
        $this->assertSecureKnownType($filterType, 42, 'int', 42, FILTER_VALIDATE_INT);
        $this->assertSecureKnownType($filterType, '42', 'integer', 42, FILTER_VALIDATE_INT);
        
        $this->assertSecureKnownType($filterType, 15.3, 'float', 15.3, FILTER_VALIDATE_FLOAT);
        $this->assertSecureKnownType($filterType, '15.3', 'double', 15.3, FILTER_VALIDATE_FLOAT);
        
        $this->assertSecureKnownType($filterType, true, 'bool', true, FILTER_VALIDATE_BOOLEAN);
        $this->assertSecureKnownType($filterType, false, 'bool', false, FILTER_VALIDATE_BOOLEAN);
        
        $this->assertSecureKnownType(
            $filterType,
            'myemail@mywebsite.com',
            'email',
            'myemail@mywebsite.com',
            FILTER_VALIDATE_EMAIL
        );
        
        $this->assert('test Helpers\Secure::secureKnownType with an unknown types')
            ->exception(function() {
                \BFW\Helpers\Secure::secureKnownType('atoum', 'string');
            })
                ->hasCode(\BFW\Helpers\Secure::ERR_SECURE_KNOWN_TYPE_FILTER_NOT_MANAGED)
        ;
    }
    
    public function testSecureUnknownType()
    {
        $this->assert('test Helpers\Secure::secureUnknownType (not declared as html)')
            ->string(\BFW\Helpers\Secure::secureUnknownType('atoum', 'string', false))
                ->isEqualTo('atoum')
            ->string(\BFW\Helpers\Secure::secureUnknownType(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                'string',
                false //htmlentities
            ))
                ->isEqualTo('Il est recommandé d\\\'utiliser composer pour installer')
            ->string(\BFW\Helpers\Secure::secureUnknownType(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                'string',
                true //htmlentities
            ))
                ->isEqualTo('Il est recommand&eacute; d&#039;utiliser composer pour installer')
        ;
        
        $this->assert('test Helpers\Secure::secureUnknownType (declared as html)')
            ->string(\BFW\Helpers\Secure::secureUnknownType('atoum', 'html', false))
                ->isEqualTo('atoum')
            ->string(\BFW\Helpers\Secure::secureUnknownType(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                'html',
                false //htmlentities
            ))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;')
            ->string(\BFW\Helpers\Secure::secureUnknownType(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                'html',
                true //htmlentities
            ))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;')
        ;
    }
    
    public function testSecureData()
    {
        //We can not mock anything into :/
        //So we test only the return and not the args passed to called method inside
        
        $this->assert('test Helpers\Secure::secureData with an integer value')
            ->integer(\BFW\Helpers\Secure::secureData(42, 'integer', false))
                ->isEqualTo(42)
        ;
        
        $this->assert('test Helpers\Secure::secureData with a string value (not declared as html)')
            ->string(\BFW\Helpers\Secure::secureData('atoum', 'string', false))
                ->isEqualTo('atoum')
            ->string(\BFW\Helpers\Secure::secureData(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                'string',
                false //htmlentities
            ))
                ->isEqualTo('Il est recommandé d\\\'utiliser composer pour installer')
            ->string(\BFW\Helpers\Secure::secureData(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                'string',
                true //htmlentities
            ))
                ->isEqualTo('Il est recommand&eacute; d&#039;utiliser composer pour installer')
        ;
        
        $this->assert('test Helpers\Secure::secureData with a string value (declared as html)')
            ->string(\BFW\Helpers\Secure::secureData('atoum', 'html', false))
                ->isEqualTo('atoum')
            ->string(\BFW\Helpers\Secure::secureData(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                'html',
                false //htmlentities
            ))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;')
            ->string(\BFW\Helpers\Secure::secureData(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                'html',
                true //htmlentities
            ))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;')
        ;
        
        $this->assert('test Helpers\Secure::secureData with an array value (not declared as html)')
            ->array(\BFW\Helpers\Secure::secureData(
                [
                    'id'      => 42,
                    'titre'   => 'install',
                    'content' => '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                ],
                'string',
                true //htmlentities
            ))
                ->isEqualTo([
                    'id'      => '42',
                    'titre'   => 'install',
                    'content' => 'Il est recommand&eacute; d&#039;utiliser composer pour installer',
                ])
        ;
        
        $this->assert('test Helpers\Secure::secureData with an array value (declared as html)')
            ->array(\BFW\Helpers\Secure::secureData(
                [
                    'id'      => 42,
                    'titre'   => 'install',
                    'content' => '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                ],
                'html',
                true //htmlentities
            ))
                ->isEqualTo([
                    'id'      => '42',
                    'titre'   => 'install',
                    'content' => '&lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;',
                ])
        ;
    }
    
    public function testGetSecureKeyInArray()
    {
        //We can not mock anything into :/
        //So we test only the return and not the args passed to called method inside
        
        $this->assert('test Helpers\Secure::getSecureKeyInArray with not existing key')
            ->exception(function() {
                $array = [];
                
                \BFW\Helpers\Secure::getSecureKeyInArray(
                    $array,
                    'libs',
                    'string'
                );
            })
                ->hasCode(\BFW\Helpers\Secure::ERR_SECURE_ARRAY_KEY_NOT_EXIST)
        ;
        
        $this->assert('test Helpers\Secure::getSecureKeyInArray with a existing key')
            ->given($arrayText = [
                'id'      => 42,
                'titre'   => 'install',
                'content' => " \n\t\n".' <p>Il est recommandé d\'utiliser composer pour installer</p>',
            ])
            ->string(\BFW\Helpers\Secure::getSecureKeyInArray(
                    $arrayText,
                    'content',
                    'string',
                    true, //htmlentities
                    true //inline
            ))
                ->isEqualTo('Il est recommand&eacute; d&#039;utiliser composer pour installer')
            ->then
            ->given($arrayHtml = [
                'id'      => 42,
                'titre'   => 'install',
                'content' => " \n\t\n".' <p>Il est recommandé d\'utiliser composer pour installer</p>',
            ])
            ->string(\BFW\Helpers\Secure::getSecureKeyInArray(
                    $arrayHtml,
                    'content',
                    'html',
                    true, //htmlentities
                    true //inline
            ))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;')
            ->then
            ->given($arrayHtml = [
                'id'      => 42,
                'titre'   => 'install',
                'content' => " \n\t\n".' <p>Il est recommandé d\'utiliser composer pour installer</p>',
            ])
            ->string(\BFW\Helpers\Secure::getSecureKeyInArray(
                    $arrayHtml,
                    'content',
                    'html',
                    true, //htmlentities
                    false //inline
            ))
                ->isEqualTo("\n\t\n".' &lt;p&gt;Il est recommand&eacute; d&#039;utiliser composer pour installer&lt;/p&gt;')
        ;
    }
    
    public function testGetManySecureKeys()
    {
        //We can not mock anything into :/
        //So we test only the return and not the args passed to called method inside
        
        $this->given($testedArray = [
            'id'      => 42,
            'titre'   => 'install',
            'content' => " \n\t\n".' <p>Il est recommandé d\'utiliser composer pour installer</p>',
        ]);
        
        $this->assert('test Helpers\Secure::getManySecureKeys with all existing keys (not inline, not html)')
            ->array(\BFW\Helpers\Secure::getManySecureKeys(
                $testedArray,
                [
                    'titre'   => 'string',
                    'content' => [
                        'type'         => 'string',
                        'htmlentities' => true,
                        'inline'       => false
                    ]
                ]
            ))
                ->isEqualTo([
                    'titre'   => 'install',
                    'content' => "\n\t\n".' Il est recommand&eacute; d&#039;utiliser composer pour installer'
                ])
        ;
        
        $this->assert('test Helpers\Secure::getManySecureKeys with all existing keys (not inline, but html)')
            ->array(\BFW\Helpers\Secure::getManySecureKeys(
                $testedArray,
                [
                    'titre'   => 'string',
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
        
        $this->assert('test Helpers\Secure::getManySecureKeys with all existing keys (inline, not html)')
            ->array(\BFW\Helpers\Secure::getManySecureKeys(
                $testedArray,
                [
                    'titre'   => 'string',
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
        
        $this->assert('test Helpers\Secure::getManySecureKeys with a not existing key and not exception')
            ->array(\BFW\Helpers\Secure::getManySecureKeys(
                $testedArray,
                [
                    'titre'  => 'string',
                    'banner' => 'string'
                ],
                false
            ))
                ->isEqualTo([
                    'titre'  => 'install',
                    'banner' => null
                ])
        ;
        
        $this->assert('test Helpers\Secure::getManySecureKeys with a not existing key and with exception')
            ->exception(function() use (&$testedArray) {
                \BFW\Helpers\Secure::getManySecureKeys(
                    $testedArray,
                    [
                        'titre'  => 'string',
                        'banner' => 'string'
                    ],
                    true
                );
            })
                ->hasCode(\BFW\Helpers\Secure::ERR_SECURE_ARRAY_KEY_NOT_EXIST)
        ;
    }
}

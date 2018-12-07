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
        $this->assert('test Helpers\Secure::secureUnknownType')
            ->string(\BFW\Helpers\Secure::secureUnknownType('atoum', false))
                ->isEqualTo('atoum')
            ->string(\BFW\Helpers\Secure::secureUnknownType(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                false
            ))
                ->isEqualTo('<p>Il est recommandé d\\\'utiliser composer pour installer</p>')
            ->string(\BFW\Helpers\Secure::secureUnknownType(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                true
            ))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d\\\'utiliser composer pour installer&lt;/p&gt;')
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
        
        $this->assert('test Helpers\Secure::secureData with a string value')
            ->string(\BFW\Helpers\Secure::secureData('atoum', 'string', false))
                ->isEqualTo('atoum')
            ->string(\BFW\Helpers\Secure::secureData(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                'string',
                false
            ))
                ->isEqualTo('<p>Il est recommandé d\\\'utiliser composer pour installer</p>')
            ->string(\BFW\Helpers\Secure::secureData(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                'string',
                true
            ))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d\\\'utiliser composer pour installer&lt;/p&gt;')
        ;
        
        $this->assert('test Helpers\Secure::secureData with an array value')
            ->array(\BFW\Helpers\Secure::secureData(
                [
                    'id'      => 42,
                    'titre'   => 'install',
                    'content' => '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                ],
                'string',
                true
            ))
                ->isEqualTo([
                    'id'      => '42',
                    'titre'   => 'install',
                    'content' => '&lt;p&gt;Il est recommand&eacute; d\\\'utiliser composer pour installer&lt;/p&gt;',
                ])
        ;
    }
    
    public function testGetSqlSecureMethod()
    {
        $this->assert('test Helpers\Secure::getSqlSecureMethod without method configured')
            ->variable(\BFW\Helpers\Secure::getSqlSecureMethod())
                ->isNull()
        ;
        
        $this->assert('test Helpers\Secure::getSqlSecureMethod with a callable function configured')
            ->if($this->app->getConfig()->setConfigKeyForFilename(
                'global.php',
                'sqlSecureMethod',
                '\BFW\Test\Helpers\secureSqlMethod'
            ))
            ->string(\BFW\Helpers\Secure::getSqlSecureMethod())
                ->isEqualTo('\BFW\Test\Helpers\secureSqlMethod')
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
            ->given($array = [
                'id'      => 42,
                'titre'   => 'install',
                'content' => '<p>Il est recommandé d\'utiliser composer pour installer</p>',
            ])
            ->string(\BFW\Helpers\Secure::getSecureKeyInArray(
                    $array,
                    'content',
                    'string',
                    true
            ))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d\\\'utiliser composer pour installer&lt;/p&gt;')
        ;
    }
    
    public function testGetManySecureKeys()
    {
        //We can not mock anything into :/
        //So we test only the return and not the args passed to called method inside
        
        $this->given($testedArray = [
            'id'      => 42,
            'titre'   => 'install',
            'content' => '<p>Il est recommandé d\'utiliser composer pour installer</p>',
        ]);
        
        $this->assert('test Helpers\Secure::getManySecureKeys with all existing keys')
            ->array(\BFW\Helpers\Secure::getManySecureKeys(
                $testedArray,
                [
                    'titre'   => 'string',
                    'content' => [
                        'type'         => 'string',
                        'htmlentities' => true
                    ]
                ]
            ))
                ->isEqualTo([
                    'titre'   => 'install',
                    'content' => '&lt;p&gt;Il est recommand&eacute; d\\\'utiliser composer pour installer&lt;/p&gt;'
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

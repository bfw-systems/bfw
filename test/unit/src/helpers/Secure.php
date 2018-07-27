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
    
    protected function assertSecuriseKnownTypes(
        &$filterType,
        $dataToTest,
        $filter,
        $expectedData,
        $expectedFilter
    ) {
        $this->assert('test Helpers\Secure::securiseKnownTypes with '.$filter.' types')
            ->given($filterType = null)
            ->variable(\BFW\Helpers\Secure::securiseKnownTypes($dataToTest, $filter))
                ->isIdenticalTo($expectedData)
            ->variable($filterType)
                ->isIdenticalTo($expectedFilter)
        ;
    }
    
    public function testSecuriseKnownTypes()
    {
        $this->assert('test Helpers\Secure::securiseKnownTypes - prepare')
            ->given($filterType = null)
            ->given($this->function->filter_var = function($variable, $filter) use (&$filterType) {
                $filterType = $filter;
                
                return \filter_var($variable, $filter);
            })
        ;
        
        $this->assertSecuriseKnownTypes($filterType, 42, 'int', 42, FILTER_VALIDATE_INT);
        $this->assertSecuriseKnownTypes($filterType, '42', 'integer', 42, FILTER_VALIDATE_INT);
        
        $this->assertSecuriseKnownTypes($filterType, 15.3, 'float', 15.3, FILTER_VALIDATE_FLOAT);
        $this->assertSecuriseKnownTypes($filterType, '15.3', 'double', 15.3, FILTER_VALIDATE_FLOAT);
        
        $this->assertSecuriseKnownTypes($filterType, true, 'bool', true, FILTER_VALIDATE_BOOLEAN);
        $this->assertSecuriseKnownTypes($filterType, false, 'bool', false, FILTER_VALIDATE_BOOLEAN);
        
        $this->assertSecuriseKnownTypes(
            $filterType,
            'myemail@mywebsite.com',
            'email',
            'myemail@mywebsite.com',
            FILTER_VALIDATE_EMAIL
        );
        
        $this->assert('test Helpers\Secure::securiseKnownTypes with an unknown types')
            ->exception(function() {
                \BFW\Helpers\Secure::securiseKnownTypes('atoum', 'string');
            })
                ->hasCode(\BFW\Helpers\Secure::ERR_SECURE_UNKNOWN_TYPE)
        ;
    }
    
    public function testSecurise()
    {
        //We can not mock anything into :/
        //So we test only the return and not the args passed to called method inside
        
        $this->assert('test Helpers\Secure::securise with an integer value')
            ->integer(\BFW\Helpers\Secure::securise(42, 'integer', false))
                ->isEqualTo(42)
        ;
        
        $this->assert('test Helpers\Secure::securise with a string value')
            ->string(\BFW\Helpers\Secure::securise('atoum', 'string', false))
                ->isEqualTo('atoum')
            ->string(\BFW\Helpers\Secure::securise(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                'string',
                false
            ))
                ->isEqualTo('<p>Il est recommandé d\\\'utiliser composer pour installer</p>')
            ->string(\BFW\Helpers\Secure::securise(
                '<p>Il est recommandé d\'utiliser composer pour installer</p>',
                'string',
                true
            ))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d\\\'utiliser composer pour installer&lt;/p&gt;')
        ;
        
        $this->assert('test Helpers\Secure::securise with an array value')
            ->array(\BFW\Helpers\Secure::securise(
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
            ->boolean(\BFW\Helpers\Secure::getSqlSecureMethod())
                ->isFalse()
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
    
    public function testGetSecurisedKeyInArray()
    {
        //We can not mock anything into :/
        //So we test only the return and not the args passed to called method inside
        
        $this->assert('test Helpers\Secure::getSecurisedKeyInArray with not existing key')
            ->exception(function() {
                $array = [];
                
                \BFW\Helpers\Secure::getSecurisedKeyInArray(
                    $array,
                    'libs',
                    'string'
                );
            })
                ->hasCode(\BFW\Helpers\Secure::ERR_SECURE_ARRAY_KEY_NOT_EXIST)
        ;
        
        $this->assert('test Helpers\Secure::getSecurisedKeyInArray with a existing key')
            ->given($array = [
                'id'      => 42,
                'titre'   => 'install',
                'content' => '<p>Il est recommandé d\'utiliser composer pour installer</p>',
            ])
            ->string(\BFW\Helpers\Secure::getSecurisedKeyInArray(
                    $array,
                    'content',
                    'string',
                    true
            ))
                ->isEqualTo('&lt;p&gt;Il est recommand&eacute; d\\\'utiliser composer pour installer&lt;/p&gt;')
        ;
    }
    
    public function testObtainManyKeys()
    {
        //We can not mock anything into :/
        //So we test only the return and not the args passed to called method inside
        
        $this->given($testedArray = [
            'id'      => 42,
            'titre'   => 'install',
            'content' => '<p>Il est recommandé d\'utiliser composer pour installer</p>',
        ]);
        
        $this->assert('test Helpers\Secure::getSecurisedManyKeys with all existing keys')
            ->array(\BFW\Helpers\Secure::getSecurisedManyKeys(
                $testedArray,
                [
                    'titre'   => 'string',
                    'content' => (object) [
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
        
        $this->assert('test Helpers\Secure::getSecurisedManyKeys with a not existing key and not exception')
            ->array(\BFW\Helpers\Secure::getSecurisedManyKeys(
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
        
        $this->assert('test Helpers\Secure::getSecurisedManyKeys with a not existing key and with exception')
            ->exception(function() use (&$testedArray) {
                \BFW\Helpers\Secure::getSecurisedManyKeys(
                    $testedArray,
                    [
                        'titre'  => 'string',
                        'banner' => 'string'
                    ],
                    true
                );
            })
                ->hasCode(\BFW\Helpers\Secure::ERR_OBTAIN_KEY)
        ;
    }
}

<?php

namespace BFW\Helpers\test\unit;

use \atoum;
use \BFW\Helpers\Secure as BfwSecure;
use \BFW\test\helpers\ApplicationInit as AppInit;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Secure extends atoum
{
    protected $app;
    
    public function beforeTestMethod($testMethod)
    {
        $this->app = AppInit::init();
    }
    
    public static function secureMethod($str)
    {
        return 'testSecurised_'.$str;
    }
    
    public function testHash()
    {
        $this->assert('test Secure::hash')
            ->string($hashed = BfwSecure::hash('test'))
                ->length
                    ->isEqualTo(64);
    }
    
    public function testSecuriseKnownTypes()
    {
        $this->assert('test Secure::securiseKnownTypes for int and integer types')
            ->integer(BfwSecure::securiseKnownTypes(10, 'int'))
                ->isEqualTo(10)
            ->integer(BfwSecure::securiseKnownTypes(10, 'integer'))
                ->isEqualTo(10)
            ->integer(BfwSecure::securiseKnownTypes('10', 'integer'))
                ->isEqualTo(10)
            ->boolean(BfwSecure::securiseKnownTypes('test', 'int'))
                ->isFalse();
        
        $this->assert('test Secure::securiseKnownTypes for float and double types')
            ->float(BfwSecure::securiseKnownTypes(10.2, 'float'))
                ->isEqualTo(10.2)
            ->float(BfwSecure::securiseKnownTypes(10.2, 'double'))
                ->isEqualTo(10.2)
            ->float(BfwSecure::securiseKnownTypes('10', 'float'))
                ->isEqualTo(10.0)
            ->boolean(BfwSecure::securiseKnownTypes('test', 'double'))
                ->isFalse();
        
        $this->assert('test Secure::securiseKnownTypes for bool and boolean types')
            ->boolean(BfwSecure::securiseKnownTypes(true, 'bool'))
                ->isTrue()
            ->boolean(BfwSecure::securiseKnownTypes(false, 'boolean'))
                ->isFalse()
            ->boolean(BfwSecure::securiseKnownTypes('true', 'bool'))
                ->isTrue()
            ->boolean(BfwSecure::securiseKnownTypes('test', 'boolean'))
                ->isFalse();
        
        $this->assert('test Secure::securiseKnownTypes for email type')
            ->boolean(BfwSecure::securiseKnownTypes('vmATbulton.fr', 'email'))
                ->isFalse()
            ->string(BfwSecure::securiseKnownTypes('vm@bulton.fr', 'email'))
                ->isEqualTo('vm@bulton.fr');
        
        $this->assert('test Secure::securiseKnownTypes exception with other type')
            ->exception(function() {
                BfwSecure::securiseKnownTypes('test securise', 'text');
            })
                ->hasMessage('Unknown type')
            ->exception(function() {
                BfwSecure::securiseKnownTypes('test securise', 'mixed');
            })
                ->hasMessage('Unknown type');
    }
    
    public function testSecurise()
    {
        $this->app->updateKey('sqlSecureMethod', '');
        
        $this->assert('test Secure::securise for direct data')
            ->integer(BfwSecure::securise('10', 'int', false))
                ->isEqualTo(10)
            ->string(BfwSecure::securise('test securise', 'text', false))
                ->isEqualTo('test securise');
        
        $this->assert('test Secure::securise for array data')
            ->array(BfwSecure::securise([
                    'a' => 'test',
                    1 => 'test2'
                ],
                'text',
                false
            ))
                ->isEqualTo([
                    'a' => 'test',
                    1 => 'test2'
                ]);
        
        $this->assert('test Secure::securise with addslashes text')
            ->string(BfwSecure::securise('it\'s a test !', 'text', false))
                ->isEqualTo('it\\\'s a test !');
        
        $this->assert('test Secure::securise with html text')
            ->string(BfwSecure::securise('<p>Test</p>', 'text', true))
                ->isEqualTo('&lt;p&gt;Test&lt;/p&gt;');
    }
    
    public function testSecuriseWithSecureMethod()
    {
        $this->assert('test Secure::securise with a secure method')
            ->if($this->app->updateKey(
                'sqlSecureMethod',
                ['\BFW\Helpers\test\unit\Secure', 'secureMethod']
            ))
            ->then
            ->string(BfwSecure::securise('test', 'text', false))
                ->isEqualTo('testSecurised_test');
    }
    
    public function testGetSqlSecureMethod()
    {
        $this->app->updateKey(
            'sqlSecureMethod',
            ['\BFW\Helpers\test\unit\Secure', 'secureMethod']
        );
        
        $this->assert('test Secure::getSqlSecureMethod')
            ->array(BfwSecure::getSqlSecureMethod())
                ->isEqualTo([
                    '\BFW\Helpers\test\unit\Secure',
                    'secureMethod'
                ]);
    }
    
    public function testGetSecurisedKeyInArray()
    {
        $this->app->updateKey('sqlSecureMethod', '');
        
        $this->assert('test Secure::getSecurisedKeyInArray')
            ->given($testedArray = [
                'a' => 'test',
                1 => 'test2'
            ])
            ->string(BfwSecure::getSecurisedKeyInArray($testedArray, 'a', 'text'))
                ->isEqualTo('test');
        
        $this->assert('test Secure::getSecurisedKeyInArray exception')
            ->exception(function() use ($testedArray) {
                BfwSecure::getSecurisedKeyInArray($testedArray, 'b', 'text');
            })
                ->hasMessage('The key b not exist');
    }
    
    public function testgetSecurisedPostKey()
    {
        $_POST = [
            'login' => 'test login',
            'password' => 'test pwd'
        ];
        
        $this->app->updateKey('sqlSecureMethod', '');
        
        $this->assert('test Secure::getSecurisedPostKey')
            ->string(BfwSecure::getSecurisedPostKey('login', 'text'))
                ->isEqualTo('test login');
    }
    
    public function testgetSecurisedGetKey()
    {
        $_GET = [
            'id' => 12350,
            'page' => 2
        ];
        
        $this->app->updateKey('sqlSecureMethod', '');
        
        $this->assert('test Secure::getSecurisedGetKey')
            ->integer(BfwSecure::getSecurisedGetKey('id', 'int'))
                ->isEqualTo(12350);
    }
}

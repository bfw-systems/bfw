<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Request extends atoum
{
    //use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        //$this->createApp();
        //$this->initApp();
        
        $this->mockGenerator
            ->makeVisible('serverValue')
            ->makeVisible('detectIp')
            ->makeVisible('detectLang')
            ->makeVisible('detectReferer')
            ->makeVisible('detectMethod')
            ->makeVisible('detectSsl')
            ->makeVisible('detectRequest')
            ->generate('BFW\Request')
        ;
        
        if ($testMethod === 'testConstructAndGetInstance') {
            return;
        }
        
        $this->mock = \mock\BFW\Request::getInstance();
    }
    
    public function testConstructAndGetInstance()
    {
        $this->assert('test Constructor')
            ->object($request = \BFW\Request::getInstance())
                ->isInstanceOf('\BFW\Request')
            ->object(\mock\BFW\Request::getInstance())
                ->isIdenticalTo($request)
        ;
    }
    
    public function testGetServerValue()
    {
        $this->assert('test Request::getServerValue with not existing key')
            ->exception(function() {
                \BFW\Request::getServerValue('atoum');
            })
                ->hasCode(\BFW\Request::ERR_KEY_NOT_EXIST)
        ;
        
        $this->assert('test Request::getServerValue with existing key')
            ->if($_SERVER['atoum'] = 'unitTest')
            ->then
            ->string(\BFW\Request::getServerValue('atoum'))
                ->isEqualTo('unitTest')
        ;
    }
    
    public function testServerValue()
    {
        //Atoum not allow to mock static method, so we can't mock the
        //return of getServerValue().
        //But it's tested before, so if there is a fail, it will be seen.
        $this->assert('test Request::serverValue with not existing key')
            ->string($this->mock->serverValue('atoum'))
                ->isEmpty()
        ;
        
        $this->assert('test Request::serverValue with existing key')
            ->if($_SERVER['atoum'] = 'unitTest')
            ->then
            ->string($this->mock->serverValue('atoum'))
                ->isEqualTo('unitTest')
        ;
    }
    
    public function testRunDetect()
    {
        $this->assert('test Request::runDetect')
            ->if($this->calling($this->mock)->detectIp = null)
            ->and($this->calling($this->mock)->detectLang = null)
            ->and($this->calling($this->mock)->detectReferer = null)
            ->and($this->calling($this->mock)->detectMethod = null)
            ->and($this->calling($this->mock)->detectSsl = null)
            ->and($this->calling($this->mock)->detectRequest = null)
            ->then
            
            ->variable($this->mock->runDetect())
                ->isNull()
            ->mock($this->mock)
                ->call('detectIp')->once()
                ->call('detectLang')->once()
                ->call('detectReferer')->once()
                ->call('detectMethod')->once()
                ->call('detectSsl')->once()
                ->call('detectRequest')->once()
        ;
    }
    
    public function testGetAndDetectIp()
    {
        $this->assert('test Request::getIp with default value')
            ->string($this->mock->getIp())
                ->isEmpty()
        ;
        
        $this->assert('test Request::detectIp and Request::getIp')
            ->if($_SERVER['REMOTE_ADDR'] = '192.168.0.255')
            ->then
            ->variable($this->mock->detectIp())
                ->isNull()
            ->string($this->mock->getIp())
                ->isEqualTo('192.168.0.255')
        ;
    }
    
    public function testGetAndDetectLang()
    {
        $this->assert('test Request::getLang with default value')
            ->string($this->mock->getLang())
                ->isEmpty()
        ;
        
        $this->assert('test Request::detectLang and Request::getLang for empty preference')
            ->if($_SERVER['HTTP_ACCEPT_LANGUAGE'] = '')
            ->then
            ->variable($this->mock->detectLang())
                ->isNull()
            ->string($this->mock->getLang())
                ->isEmpty()
        ;
        
        $this->assert('test Request::detectLang and Request::getLang with preference')
            ->if($_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4')
            ->then
            ->variable($this->mock->detectLang())
                ->isNull()
            ->string($this->mock->getLang())
                ->isEqualTo('fr')
        ;
    }
    
    public function testGetAndDetectReferer()
    {
        $this->assert('test Request::getReferer with default value')
            ->string($this->mock->getReferer())
                ->isEmpty()
        ;
        
        $this->assert('test Request::detectReferer and Request::getReferer')
            ->if($_SERVER['HTTP_REFERER'] = 'https://bfw.bulton.fr')
            ->then
            ->variable($this->mock->detectReferer())
                ->isNull()
            ->string($this->mock->getReferer())
                ->isEqualTo('https://bfw.bulton.fr')
        ;
    }
    
    public function testGetAndDetectMethod()
    {
        $this->assert('test Request::getMethod with default value')
            ->string($this->mock->getMethod())
                ->isEmpty()
        ;
        
        $this->assert('test Request::detectMethod and Request::getMethod')
            ->if($_SERVER['REQUEST_METHOD'] = 'GET')
            ->then
            ->variable($this->mock->detectMethod())
                ->isNull()
            ->string($this->mock->getMethod())
                ->isEqualTo('GET')
        ;
    }
    
    public function testGetAndDetectSsl()
    {
        $this->assert('test Request::getSsl with default value')
            ->variable($this->mock->getSsl())
                ->isNull()
        ;
        
        $this->assert('test Request::detectSsl and Request::getSsl for no ssl')
            ->if($_SERVER['HTTPS'] = '')
            ->and($_SERVER['HTTP_X_FORWARDED_PROTO'] = '')
            ->and($_SERVER['HTTP_X_FORWARDED_SSL'] = '')
            ->then
            ->variable($this->mock->detectSsl())
                ->isNull()
            ->boolean($this->mock->getSsl())
                ->isFalse()
        ;
        
        $this->assert('test Request::detectSsl and Request::getSsl for HTTP_X_FORWARDED_SSL')
            ->if($_SERVER['HTTPS'] = '')
            ->and($_SERVER['HTTP_X_FORWARDED_PROTO'] = '')
            ->and($_SERVER['HTTP_X_FORWARDED_SSL'] = 'on')
            ->then
            ->variable($this->mock->detectSsl())
                ->isNull()
            ->boolean($this->mock->getSsl())
                ->isTrue()
        ;
        
        $this->assert('test Request::detectSsl and Request::getSsl for HTTP_X_FORWARDED_PROTO')
            ->if($_SERVER['HTTPS'] = '')
            ->and($_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https')
            ->and($_SERVER['HTTP_X_FORWARDED_SSL'] = '')
            ->then
            ->variable($this->mock->detectSsl())
                ->isNull()
            ->boolean($this->mock->getSsl())
                ->isTrue()
        ;
        
        $this->assert('test Request::detectSsl and Request::getSsl for HTTPS')
            ->if($_SERVER['HTTPS'] = 'on')
            ->and($_SERVER['HTTP_X_FORWARDED_PROTO'] = '')
            ->and($_SERVER['HTTP_X_FORWARDED_SSL'] = '')
            ->then
            ->variable($this->mock->detectSsl())
                ->isNull()
            ->boolean($this->mock->getSsl())
                ->isTrue()
        ;
    }
    
    public function testGetAndDetectRequest()
    {
        $this->assert('test Request::getRequest with default value')
            ->variable($this->mock->getRequest())
                ->isNull()
        ;
        
        $this->assert('test Request::detectRequest and Request::getRequest with empty infos')
            ->if($_SERVER['REQUEST_URI'] = '')
            ->and($_SERVER['HTTP_HOST'] = '')
            ->and($_SERVER['SERVER_PORT'] = '')
            ->and($_SERVER['PHP_AUTH_USER'] = '')
            ->and($_SERVER['PHP_AUTH_PW'] = '')
            ->then
            ->variable($this->mock->detectRequest())
                ->isNull()
            ->object($this->mock->getRequest())
                ->isEqualTo((object) [
                    'scheme'   => 'http',
                    'host'     => '',
                    'port'     => '',
                    'user'     => '',
                    'pass'     => '',
                    'path'     => '',
                    'query'    => '',
                    'fragment' => '',
                ])
        ;
        
        $this->assert('test Request::detectRequest and Request::getRequest with only default infos')
            ->if($_SERVER['REQUEST_URI'] = '')
            ->and($_SERVER['HTTP_HOST'] = 'bfw.bulton.fr')
            ->and($_SERVER['SERVER_PORT'] = '80')
            ->and($_SERVER['PHP_AUTH_USER'] = 'unit')
            ->and($_SERVER['PHP_AUTH_PW'] = 'atoum')
            ->then
            ->variable($this->mock->detectRequest())
                ->isNull()
            ->object($this->mock->getRequest())
                ->isEqualTo((object) [
                    'scheme'   => 'http',
                    'host'     => 'bfw.bulton.fr',
                    'port'     => '80',
                    'user'     => 'unit',
                    'pass'     => 'atoum',
                    'path'     => '',
                    'query'    => '',
                    'fragment' => '',
                ])
        ;
        
        $this->assert('test Request::detectRequest and Request::getRequest with all infos')
            ->if($_SERVER['REQUEST_URI'] = 'https://bfw.bulton.fr/wiki/v3.0/fr/introduction')
            ->and($_SERVER['HTTP_HOST'] = 'www.bulton.fr')
            ->and($_SERVER['SERVER_PORT'] = '80')
            ->and($_SERVER['PHP_AUTH_USER'] = 'unit')
            ->and($_SERVER['PHP_AUTH_PW'] = 'atoum')
            ->then
            ->variable($this->mock->detectRequest())
                ->isNull()
            ->object($this->mock->getRequest())
                ->isEqualTo((object) [
                    'scheme'   => 'https',
                    'host'     => 'bfw.bulton.fr',
                    'port'     => '80', //Not exist into REQUEST_URI
                    'user'     => 'unit', //Not exist into REQUEST_URI
                    'pass'     => 'atoum', //Not exist into REQUEST_URI
                    'path'     => '/wiki/v3.0/fr/introduction',
                    'query'    => '',
                    'fragment' => '',
                ])
        ;
    }
}
<?php

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Request extends atoum
{
    /**
     * @var $class : Instance de la class
     */
    protected $class;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        if($testMethod === 'testGetInstance') {
            return;
        }
        
        $_SERVER['HTTP_ACCEPT_LANGUAGE']   = '';
        $_SERVER['HTTP_HOST']              = '';
        $_SERVER['HTTP_REFERER']           = '';
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = '';
        $_SERVER['HTTP_X_FORWARDED_SSL']   = '';
        $_SERVER['HTTPS']                  = '';
        $_SERVER['REMOTE_ADDR']            = '';
        $_SERVER['REQUEST_METHOD']         = '';
        $_SERVER['REQUEST_URI']            = '';
        
        $this->class = \BFW\Request::getInstance();
    }
    
    public function testGetInstance()
    {
        $this->assert('test getInstance : create new instance')
            ->given($firstInstance = \BFW\Request::getInstance())
            ->object($firstInstance)
                ->isInstanceOf('\BFW\Request');
        
        $this->assert('test getInstance : get last instance')
            ->given($getInstance = \BFW\Request::getInstance())
            ->object($getInstance)
                ->isInstanceOf('\BFW\Request')
                ->isIdenticalTo($firstInstance);
    }
    
    public function testGetIp()
    {
        $this->assert('test getIp : default return')
            ->string($this->class->getIp())
                ->isEmpty();
        
        $this->assert('test getIp with a fake value')
            ->given($newValue = '192.168.0.1')
            ->given($_SERVER['REMOTE_ADDR'] = $newValue)
            ->given($this->class->runDetect())
            ->string($this->class->getIp())
                ->isEqualTo($newValue);
    }

    public function testGetLang()
    {
        $this->assert('test getLang : default return')
            ->string($this->class->getLang())
                ->isEmpty();
        
        //Thanks to http://www.albertcasadessus.com/2012/06/27/get-web-browser-preferrer-language-with-php-_server-variables-http_accept_language/
        //For somes values
        
        $fakeValues =  [
            'fr'       => (object) [
                'value'    => 'fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4',
                'expected' => 'fr'
            ],
            'caMoz'    => (object) [
                'value'    => 'ca,en-us;q=0.7,en;q=0.3',
                'expected' => 'ca'
            ],
            'caIE'     => (object) [
                'value'    => 'es-ES',
                'expected' => 'es'
            ],
            'caChr'    => (object) [
                'value'    => 'ca-ES,ca;q=0.8',
                'expected' => 'ca'
            ]
        ];
        
        foreach ($fakeValues as $name => $infos) {
            $this->assert('test getLang with a fake value ('.$name.')')
                ->given($_SERVER['HTTP_ACCEPT_LANGUAGE'] = $infos->value)
                ->given($this->class->runDetect())
                ->string($this->class->getLang())
                    ->isEqualTo($infos->expected);
        }
    }

    public function testGetReferer()
    {
        $this->assert('test getReferer : default return')
            ->string($this->class->getReferer())
                ->isEmpty();
        
        $this->assert('test getReferer with a fake value')
            ->given($newValue = 'http://www.bulton.fr/')
            ->given($_SERVER['HTTP_REFERER'] = $newValue)
            ->given($this->class->runDetect())
            ->string($this->class->getReferer())
                ->isEqualTo($newValue);
    }

    public function testGetMethod()
    {
        $this->assert('test getMethod : default return')
            ->string($this->class->getMethod())
                ->isEmpty();
        
        $this->assert('test getMethod with a fake value (GET)')
            ->given($newValue = 'GET')
            ->given($_SERVER['REQUEST_METHOD'] = $newValue)
            ->given($this->class->runDetect())
            ->string($this->class->getMethod())
                ->isEqualTo($newValue);
        
        $this->assert('test getMethod with a fake value (POST)')
            ->given($newValue = 'POST')
            ->given($_SERVER['REQUEST_METHOD'] = $newValue)
            ->given($this->class->runDetect())
            ->string($this->class->getMethod())
                ->isEqualTo($newValue);
    }

    public function testGetSsl()
    {
        $this->assert('test getSsl : default return')
            ->boolean($this->class->getSsl())
                ->isFalse();
        
        $this->assert('test getSsl with a fake value for HTTP_X_FORWARDED_SSL')
            ->given($_SERVER['HTTP_X_FORWARDED_SSL'] = 'on')
            ->given($this->class->runDetect())
            ->boolean($this->class->getSsl())
                ->isTrue();
        
        $this->assert('test getSsl with a fake value for HTTP_X_FORWARDED_PROTO')
            ->given($_SERVER['HTTP_X_FORWARDED_SSL'] = 'off')
            ->given($_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https')
            ->given($this->class->runDetect())
            ->boolean($this->class->getSsl())
                ->isTrue();
        
        $this->assert('test getSsl with a fake value for HTTPS')
            ->given($_SERVER['HTTP_X_FORWARDED_PROTO'] = 'off')
            ->given($_SERVER['HTTPS'] = 'on')
            ->given($this->class->runDetect())
            ->boolean($this->class->getSsl())
                ->isTrue();
    }

    public function testGetRequest()
    {
        $this->assert('test getRequest : default return')
            ->object($request = $this->class->getRequest())
                ->string($request->scheme)->isEmpty()
                ->string($request->host)->isEmpty()
                ->string($request->port)->isEmpty()
                ->string($request->user)->isEmpty()
                ->string($request->pass)->isEmpty()
                ->string($request->path)->isEmpty()
                ->string($request->query)->isEmpty()
                ->string($request->fragment)->isEmpty();
        
        $fakeUrl = 'https://github.com/bulton-fr/bfw/blob/3.0/.atoum.php?fa=ke&foo=bar#L1';
        $this->assert('test getRequest with fake url')
            ->given($_SERVER['REQUEST_URI'] = $fakeUrl)
            ->given($_SERVER['HOST'] = 'github.com')
            ->given($this->class->runDetect())
            ->object($request = $this->class->getRequest())
                ->string($request->scheme)->isEqualTo('https')
                ->string($request->host)->isEqualTo('github.com')
                ->string($request->port)->isEqualTo('')
                ->string($request->user)->isEqualTo('')
                ->string($request->pass)->isEqualTo('')
                ->string($request->path)->isEqualTo('/bulton-fr/bfw/blob/3.0/.atoum.php')
                ->string($request->query)->isEqualTo('fa=ke&foo=bar')
                ->string($request->fragment)->isEqualTo('L1');
    }

    public function testGetServerVar()
    {
        $this->assert('test getServerVar with default value')
            ->string(\BFW\Request::getServerVar('HOST'))
                ->isEmpty();
        
        $this->assert('test getServerVar with a fake value')
            ->given($_SERVER['HOST'] = 'bulton.fr')
            ->string(\BFW\Request::getServerVar('HOST'))
                ->isEqualTo('bulton.fr');
        
        $this->assert('test getServerVar with an unknown value')
            ->string(\BFW\Request::getServerVar('BULTON'))
                ->isEmpty();    
    }
}

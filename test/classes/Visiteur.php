<?php
/**
 * Fichier de test pour une class
 */

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../common.php');

/**
 * Test de la class Visiteur
 */
class Visiteur extends atoum
{
    /**
     * @var $class : Instance de la class Visiteur
     */
    protected $class;

    /**
     * @var $mock : Instance du mock pour la class Visiteur
     */
    protected $mock;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        $_SERVER = array(
            'HTTP_HOST'       => 'bfw.bulton.fr',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_ACCEPT'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36',
            'HTTP_ACCEPT_ENCODING' => 'gzip,deflate,sdch',
            'HTTP_ACCEPT_LANGUAGE' => 'fr,fr-FR;q=0.8,en-US;q=0.6,en;q=0.4',
            'SERVER_NAME' => 'bfw.bulton.fr',
            'SERVER_ADDR' => '46.105.37.1',
            'REMOTE_ADDR' => '46.105.37.1',
            'REQUEST_URI' => '/test.php',
        );
        
        //$this->class = new \BFW\Visiteur();
        $this->mock  = new MockVisiteur();
    }
    
    /**
     * Test de la méthode getIdSession()
     */
    public function testGetIdSession()
    {
        $this->variable($this->mock->getIdSession())->isNull();
    }
    
    /**
     * Test de la méthode getIp()
     */
    public function testGetIp()
    {
        $this->string($this->mock->getIp())->isEqualTo('46.105.37.1');
    }
    
    /**
     * Test de la méthode getHost()
     */
    public function testGetHost()
    {
        $this->string($this->mock->getHost())->isEqualTo('');
    }
    
    /**
     * Test de la méthode getProxy()
     */
    public function testGetProxy()
    {
        $this->variable($this->mock->getProxy())->isNull();
    }
    
    /**
     * Test de la méthode getProxyIp()
     */
    public function testGetProxyIp()
    {
        $this->string($this->mock->getProxyIp())->isEqualTo('');
    }
    
    /**
     * Test de la méthode getProxyHost()
     */
    public function testGetProxyHost()
    {
        $this->string($this->mock->getProxyHost())->isEqualTo('');
    }
    
    /**
     * Test de la méthode getOs()
     */
    public function testGetOs()
    {
        $this->string($this->mock->getOs())->isEqualTo('Windows 7');
    }
    
    /**
     * Test de la méthode getNav()
     */
    public function testGetNav()
    {
        $this->string($this->mock->getNav())->isEqualTo('Chrome');
    }
    
    /**
     * Test de la méthode getLangue()
     */
    public function testGetLangue()
    {
        $this->string($this->mock->getLangue())->isEqualTo('Français');
    }
    
    /**
     * Test de la méthode getLangueInitiale()
     */
    public function testGetLangueInitiale()
    {
        $this->string($this->mock->getLangueInitiale())->isEqualTo('fr');
    }
    
    /**
     * Test de la méthode getProviens()
     */
    public function testGetProviens()
    {
        $this->string($this->mock->getProviens())->isEqualTo('Inconnu');
    }
    
    /**
     * Test de la méthode getUrl()
     */
    public function testGetUrl()
    {
        $this->string($this->mock->getUrl())->isEqualTo('http://bfw.bulton.fr/test.php');
    }
    
    /**
     * Test de la méthode getBot()
     */
    public function testGetBot()
    {
        $this->string($this->mock->getBot())->isEqualTo('');
    }

    /**
     * Test du constructeur : Visiteur()
     */
    public function testVisiteur()
    {
        $this->mock = new MockVisiteur();
        $this->variable($this->mock->idSession)->isNull();
        $this->object($this->mock->_kernel)->isInstanceOf('\BFW\Kernel');
        
        $_SESSION['idSess'] = 'monId';
        $this->mock = new MockVisiteur();
        $this->string($this->mock->idSession)->isEqualTo('monId');
    }

    /**
     * Test de la méthode proxyDetect()
     * 
     * @TODO : Validate proxy detector before
     */
    public function testProxyDetect()
    {
        
    }

    /**
     * Test de la méthode proxyIpDetect()
     * 
     * @TODO : Validate proxy detector before
     */
    public function testProxyIpDetect()
    {
        
    }

    /**
     * Test de la méthode proxyHostDetect()
     * 
     * @TODO : Validate proxy detector before
     */
    public function testProxyHostDetect()
    {
        
    }

    /**
     * Test de la méthode realIpDetect()
     * 
     * @TODO : Validate proxy detector before
     */
    public function testRealIpDetect()
    {
        //Without proxy
        $this->mock->realIpDetect();
        $this->string($this->mock->ip)->isEqualTo('46.105.37.1');
        
        //With proxy : Todo
    }

    /**
     * Test de la méthode systemDetect()
     */
    public function testSystemDetect()
    {
        $this->mock->systemDetect();
        $this->string($this->mock->os)->isEqualTo('Windows 7');
        
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6';
        $this->mock->systemDetect();
        $this->string($this->mock->os)->isEqualTo('Windows XP');
        
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64; rv:15.0) Gecko/20120724 Debian Iceweasel/15.0';
        $this->mock->systemDetect();
        $this->string($this->mock->os)->isEqualTo('Linux');
        
        $_SERVER['HTTP_USER_AGENT'] = 'Opera/9.80 (Android; Opera Mini/7.5.33361/31.1350; U; en) Presto/2.8.119 Version/11.10';
        $this->mock->systemDetect();
        $this->string($this->mock->os)->isEqualTo('Android');
        
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36';
        $this->mock->systemDetect();
        $this->string($this->mock->os)->isEqualTo('Macintosh');
        
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25';
        $this->mock->systemDetect();
        $this->string($this->mock->os)->isEqualTo('Macintosh');
        
        $_SERVER['HTTP_USER_AGENT'] = '';
        $this->mock->systemDetect();
        $this->string($this->mock->os)->isEqualTo('Inconnu');
    }

    /**
     * Test de la méthode browserDetect()
     */
    public function testBrowserDetect()
    {
        $this->mock->browserDetect();
        $this->string($this->mock->nav)->isEqualTo('Chrome');
        
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6';
        $this->mock->browserDetect();
        $this->string($this->mock->nav)->isEqualTo('Mozilla Firefox');
        
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64; rv:15.0) Gecko/20120724 Debian Iceweasel/15.0';
        $this->mock->browserDetect();
        $this->string($this->mock->nav)->isEqualTo('Mozilla');
        
        $_SERVER['HTTP_USER_AGENT'] = 'Opera/9.80 (Android; Opera Mini/7.5.33361/31.1350; U; en) Presto/2.8.119 Version/11.10';
        $this->mock->browserDetect();
        $this->string($this->mock->nav)->isEqualTo('Opera');
        
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25';
        $this->mock->browserDetect();
        $this->string($this->mock->nav)->isEqualTo('Safari');
        
        $_SERVER['HTTP_USER_AGENT'] = 'Googlebot/2.1 ( http://www.googlebot.com/bot.html) ';
        $this->mock->browserDetect();
        $this->string($this->mock->nav)->isEqualTo('Search engine');
        $this->string($this->mock->bot)->isEqualTo('Google');
        
        $_SERVER['HTTP_USER_AGENT'] = '';
        $this->mock->browserDetect();
        $this->string($this->mock->nav)->isEqualTo('Inconnu');
    }

    /**
     * Test de la méthode languageDetect()
     */
    public function testLanguageDetect()
    {
        $this->string($this->mock->languageDetect())->isEqualTo('fr');
        
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'es,en-us;q=0.3,de;q=0.1';
        $this->string($this->mock->languageDetect())->isEqualTo('es');
        
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-us;q=0.3,de;q=0.1';
        $this->string($this->mock->languageDetect())->isEqualTo('en');
    }

    /**
     * Test de la méthode languageConvert($lang='')
     */
    public function testLanguageConvert()
    {
        $this->string($this->mock->languageConvert('fr'))->isEqualTo('Français');
        $this->string($this->mock->languageConvert('frr'))->isEqualTo('Inconnue');
    }

    /**
     * Test de la méthode refererDetect()
     */
    public function testRefererDetect()
    {
        $this->mock->refererDetect();
        $this->string($this->mock->proviens)->isEqualTo('Inconnu');
        
        $_SERVER['HTTP_REFERER'] = 'http://www.google.fr';
        $this->mock->refererDetect();
        $this->string($this->mock->proviens)->isEqualTo('http://www.google.fr');
    }

    /**
     * Test de la méthode uriDetect()
     */
    public function testUriDetect()
    {
        $this->mock->uriDetect();
        $this->string($this->mock->url)->isEqualTo('http://bfw.bulton.fr/test.php');
        
        $_SERVER['REQUEST_URI'] = '';
        $this->mock->uriDetect();
        $this->string($this->mock->url)->isEqualTo('Inconnu');
        
        unset($_SERVER['REQUEST_URI']);
        $this->mock->uriDetect();
        $this->string($this->mock->url)->isEqualTo('Inconnu');
    }

}

/**
 * Mock pour la classe Visiteur
 */
class MockVisiteur extends \BFW\Visiteur
{
    /**
     * Accesseur get
     */
    public function __get($name) {return $this->$name;}

    /**
     * Test de la méthode recupInfos()
     */
    public function recupInfos()
    {
        return parent::recupInfos();
    }

    /**
     * Test de la méthode proxyDetect()
     */
    public function proxyDetect()
    {
        return parent::proxyDetect();
    }

    /**
     * Test de la méthode proxyIpDetect()
     */
    public function proxyIpDetect()
    {
        return parent::proxyIpDetect();
    }

    /**
     * Test de la méthode proxyHostDetect()
     */
    public function proxyHostDetect()
    {
        return parent::proxyHostDetect();
    }

    /**
     * Test de la méthode realIpDetect()
     */
    public function realIpDetect()
    {
        return parent::realIpDetect();
    }

    /**
     * Test de la méthode realHostDetect()
     */
    public function realHostDetect()
    {
        return parent::realHostDetect();
    }

    /**
     * Test de la méthode systemDetect()
     */
    public function systemDetect()
    {
        return parent::systemDetect();
    }

    /**
     * Test de la méthode browserDetect()
     */
    public function browserDetect()
    {
        return parent::browserDetect();
    }

    /**
     * Test de la méthode languageDetect()
     */
    public function languageDetect()
    {
        return parent::languageDetect();
    }

    /**
     * Test de la méthode languageConvert($lang='')
     */
    public function languageConvert($lang='')
    {
        return parent::languageConvert($lang);
    }

    /**
     * Test de la méthode refererDetect()
     */
    public function refererDetect()
    {
        return parent::refererDetect();
    }

    /**
     * Test de la méthode uriDetect()
     */
    public function uriDetect()
    {
        return parent::uriDetect();
    }

}

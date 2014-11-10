<?php
/**
 * Fichier de test pour une class
 */

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../common.php');

/**
 * Test de la class Ram
 */
class Ram extends atoum
{
    /**
     * @var $class : Instance de la class Ram
     */
    protected $class;

    /**
     * @var $mock : Instance du mock pour la class Ram
     */
    protected $mock;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        //$this->class = new \BFW\Ram();
        $this->mock  = new MockRam('localhost', 11211);
        $this->mock->Server->flush();
    }

    /**
     * Test du constructeur : Ram($host, $port)
     */
    public function testRam()
    {
        $this->mock = new MockRam('localhost', 11211);
        $this->boolean($this->mock->server_connect)->isTrue();
        
        //Not test if connexion fail because we doesn't catch php warning error.
        
        $this->exception(function()
        {
            new MockRam('localhost', '11211');
        })->message->contains('Memcache connexion informations format is not correct.');
        
        $this->exception(function()
        {
            new MockRam(127, 11211);
        })->message->contains('Memcache connexion informations format is not correct.');
        
        $this->exception(function()
        {
            new MockRam(127, '11211');
        })->message->contains('Memcache connexion informations format is not correct.');
    }

    /**
     * Test de la méthode setVal($key, $data, $expire=0)
     */
    public function testSetVal()
    {
        $this->boolean($this->mock->Server->get('test'))->isFalse();
        
        $this->variable($this->mock->setVal('test', 'monTest', 0))->isEqualTo('monTest');
        $this->string($this->mock->Server->get('test'))->isEqualTo('monTest');
        
        $this->variable($this->mock->setVal('test', 'monNouveauTest', 0))->isEqualTo('monTest');
        $this->string($this->mock->Server->get('test'))->isEqualTo('monNouveauTest');
        
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->setVal(42, 'test', 0);
        })->message->contains('Erreur dans les paramètres de Ram->setVal()');
        
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->setVal(42, 'test', 'test');
        })->message->contains('Erreur dans les paramètres de Ram->setVal()');
        
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->setVal('test', opendir('/tmp'), 0);
        })->message->contains('Erreur dans les paramètres de Ram->setVal()');
    }

    /**
     * Test de la méthode majExpire($key, $exp)
     */
    public function testMajExpire()
    {
        $this->boolean($this->mock->majExpire('test', 30))->isFalse();
        
        $this->mock->setVal('test', 'monTest', 0);
        $this->boolean($this->mock->majExpire('test', 30))->isTrue();
        
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->majExpire(42, 0);
        })->message->contains('Erreur dans les paramètres de Ram->majExpire()');
        
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->majExpire(42, 'test');
        })->message->contains('Erreur dans les paramètres de Ram->majExpire()');
    }

    /**
     * Test de la méthode ifExists($key)
     */
    public function testIfExists()
    {
        $this->boolean($this->mock->ifExists('test'))->isFalse();
        
        $this->mock->setVal('test', 'monTest', 0);
        $this->boolean($this->mock->ifExists('test'))->isTrue();
        
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->ifExists(42);
        })->message->contains('Erreur dans les paramètres de Ram->ifExists()');
    }

    /**
     * Test de la méthode delete($key)
     */
    public function testDelete()
    {
        $this->boolean($this->mock->delete('test'))->isFalse();
        
        $this->mock->setVal('test', 'monTest', 0);
        $this->boolean($this->mock->delete('test'))->isTrue();
        
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->delete(42);
        })->message->contains('Erreur dans les paramètres de Ram->delete()');
    }

    /**
     * Test de la méthode getVal($key)
     */
    public function testGetVal()
    {
        $this->boolean($this->mock->getVal('test'))->isFalse();
        
        $this->mock->setVal('test', 'monTest', 0);
        $this->string($this->mock->getVal('test'))->isEqualTo('monTest');
        
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->getVal(42);
        })->message->contains('Erreur dans les paramètres de Ram->getVal()');
    }

}

/**
 * Mock pour la classe Ram
 */
class MockRam extends \BFW\Ram
{
    /**
     * Accesseur get
     */
    public function __get($name) {return $this->$name;}

}

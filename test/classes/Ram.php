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
        //$this->mock  = new MockRam();
    }

    /**
     * Test du constructeur : Ram($name=localhost)
     */
    public function testRam()
    {
        
    }

    /**
     * Test de la méthode setVal($key, $data, $expire=0)
     */
    public function testSetVal()
    {
        
    }

    /**
     * Test de la méthode majExpire($key, $exp)
     */
    public function testMajExpire()
    {
        
    }

    /**
     * Test de la méthode ifExists($key)
     */
    public function testIfExists()
    {
        
    }

    /**
     * Test de la méthode delete($key)
     */
    public function testDelete()
    {
        
    }

    /**
     * Test de la méthode getVal($key)
     */
    public function testGetVal()
    {
        
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

<?php
/**
 * Fichier de test pour une class
 */

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../common.php');

/**
 * Test de la class Cache
 */
class Cache extends atoum
{
    /**
     * @var $class : Instance de la class Cache
     */
    protected $class;

    /**
     * @var $mock : Instance du mock pour la class Cache
     */
    protected $mock;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        //$this->class = new \BFW\Cache();
        //$this->mock  = new MockCache();
    }

    /**
     * Test du constructeur : Cache($controler)
     */
    public function testCache()
    {
        
    }

    /**
     * Test de la méthode run()
     */
    public function testRun()
    {
        
    }

    /**
     * Test de la méthode set_controler($controler)
     */
    public function testSet_controler()
    {
        
    }

}

/**
 * Mock pour la classe Cache
 */
class MockCache extends \BFW\Cache
{
    /**
     * Accesseur get
     */
    public function __get($name) {return $this->$name;}
}

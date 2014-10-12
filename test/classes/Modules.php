<?php
/**
 * Fichier de test pour une class
 */

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../common.php');

/**
 * Test de la class Modules
 */
class Modules extends atoum
{
    /**
     * @var $class : Instance de la class Modules
     */
    protected $class;

    /**
     * @var $mock : Instance du mock pour la class Modules
     */
    protected $mock;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        //$this->class = new \BFW\Modules();
        //$this->mock  = new MockModules();
    }

    /**
     * Test du constructeur : Modules()
     */
    public function testModules()
    {
        
    }

    /**
     * Test de la méthode newMod($name, $params=Array)
     */
    public function testNewMod()
    {
        
    }

    /**
     * Test de la méthode exists($name)
     */
    public function testExists()
    {
        
    }

    /**
     * Test de la méthode isLoad($name)
     */
    public function testIsLoad()
    {
        
    }

    /**
     * Test de la méthode addPath($name, $path)
     */
    public function testAddPath()
    {
        
    }

    /**
     * Test de la méthode listToLoad($timeToLoad)
     */
    public function testListToLoad()
    {
        
    }

    /**
     * Test de la méthode listNotLoad($regen=)
     */
    public function testListNotLoad()
    {
        
    }

    /**
     * Test de la méthode isModulesNotLoad()
     */
    public function testIsModulesNotLoad()
    {
        
    }

    /**
     * Test de la méthode getModuleInfos($name)
     */
    public function testGetModuleInfos()
    {
        
    }

}

/**
 * Mock pour la classe Modules
 */
class MockModules extends \BFW\Modules
{
    /**
     * Accesseur get
     */
    public function __get($name) {return $this->$name;}
}

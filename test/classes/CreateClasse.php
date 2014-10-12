<?php
/**
 * Fichier de test pour une class
 */

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../common.php');

/**
 * Test de la class CreateClasse
 */
class CreateClasse extends atoum
{
    /**
     * @var $class : Instance de la class CreateClasse
     */
    protected $class;

    /**
     * @var $mock : Instance du mock pour la class CreateClasse
     */
    protected $mock;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        //$this->class = new \BFW\CreateClasse();
        //$this->mock  = new MockCreateClasse();
    }

    /**
     * Test du constructeur : CreateClasse($nom, $options=Array)
     */
    public function testCreateClasse()
    {
        
    }

    /**
     * Test de la méthode get_file()
     */
    public function testGet_file()
    {
        
    }

    /**
     * Test de la méthode createAttribut($nom, $opt=Array)
     */
    public function testCreateAttribut()
    {
        
    }

    /**
     * Test de la méthode createMethode($nom, $porter=private)
     */
    public function testCreateMethode()
    {
        
    }

    /**
     * Test de la méthode genere()
     */
    public function testGenere()
    {
        
    }

}

/**
 * Mock pour la classe CreateClasse
 */
class MockCreateClasse extends \BFW\CreateClasse
{
    /**
     * Accesseur get
     */
    public function __get($name) {return $this->$name;}
}

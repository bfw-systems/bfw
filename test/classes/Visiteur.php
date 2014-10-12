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
        //$this->class = new \BFW\Visiteur();
        //$this->mock  = new MockVisiteur();
    }

    /**
     * Test du constructeur : Visiteur()
     */
    public function testVisiteur()
    {
        
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
}

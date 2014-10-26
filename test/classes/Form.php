<?php
/**
 * Fichier de test pour une class
 */

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../common.php');

/**
 * Test de la class Form
 */
class Form extends atoum
{
    /**
     * @var $class : Instance de la class Form
     */
    protected $class;

    /**
     * @var $mock : Instance du mock pour la class Form
     */
    protected $mock;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        //$this->class = new \BFW\Form();
        //$this->mock  = new MockForm();
    }

    /**
     * Test du constructeur : Form($idForm=)
     */
    public function testForm()
    {
        
    }

    /**
     * Test de la méthode set_idForm($idForm)
     */
    public function testSet_idForm()
    {
        
    }

    /**
     * Test de la méthode create_token()
     */
    public function testCreate_token()
    {
        
    }

    /**
     * Test de la méthode verif_token()
     */
    public function testVerif_token()
    {
        
    }

}

/**
 * Mock pour la classe Form
 */
class MockForm extends \BFW\Form
{
    /**
     * Accesseur get
     */
    public function __get($name) {return $this->$name;}

}

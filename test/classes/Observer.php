<?php
/**
 * Fichier de test pour une class
 */

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../common.php');

/**
 * Test de la class Observer
 */
class Observer extends atoum
{
    /**
     * @var $class : Instance de la class Observer
     */
    protected $class;

    /**
     * @var $mock : Instance du mock pour la class Observer
     */
    protected $mock;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        //$this->class = new \BFW\Observer();
        //$this->mock  = new MockObserver();
    }

    /**
     * Test de la méthode update($subject)
     */
    public function testUpdate()
    {
        
    }

    /**
     * Test de la méthode updateWithAction($subject, $action)
     */
    public function testUpdateWithAction()
    {
        
    }

}

/**
 * Mock pour la classe Observer
 */
class MockObserver extends \BFW\Observer
{
    /**
     * Accesseur get
     */
    public function __get($name) {return $this->$name;}
}

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
        $this->mock  = new MockObserver();
    }

    /**
     * Test de la méthode update($subject)
     */
    public function testUpdate()
    {
        $subject = new MockObserverSplSubject;
        $this->mock->update($subject);
    }

    /**
     * Test de la méthode updateWithAction($subject, $action)
     * 
     * @TODO : Too complexe actually
     */
    public function testUpdateWithAction()
    {
        
    }

}

/**
 * Mock pour les observers
 */
class MockObserverSplSubject implements \SplSubject
{
    /**
     * Ajouter un nouvel observateur
     * 
     * @param SplObserver $observer L'observateur à ajouter
     */
    public function attach(SplObserver $observer)
    {
        
    }
    
    /**
     * Enlever un observateur
     * 
     * @param SplObserver $observer L'observateur à enlever
     */
    public function detach(SplObserver $observer)
    {
        
    }
    
    /**
     * Déclanche la notification vers les observers.
     */
    public function notify()
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

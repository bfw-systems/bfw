<?php
/**
 * Fichier de test pour une class
 */

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../common.php');

/**
 * Test de la class Kernel
 */
class Kernel extends atoum
{
    /**
     * @var $class : Instance de la class Kernel
     */
    protected $class;

    /**
     * @var $mock : Instance du mock pour la class Kernel
     */
    protected $mock;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        //$this->class = new \BFW\Kernel();
        //$this->mock  = new MockKernel();
    }

    /**
     * Test de la méthode attach($observer)
     */
    public function testAttach()
    {
        
    }

    /**
     * Test de la méthode attachOther($observer)
     */
    public function testAttachOther()
    {
        
    }

    /**
     * Test de la méthode detach($observer)
     */
    public function testDetach()
    {
        
    }

    /**
     * Test de la méthode detachOther($observer)
     */
    public function testDetachOther()
    {
        
    }

    /**
     * Test de la méthode notifyObserver($action)
     */
    public function testNotifyObserver()
    {
        
    }

    /**
     * Test de la méthode notifyAction($action)
     */
    public function testNotifyAction()
    {
        
    }

    /**
     * Test de la méthode notify()
     */
    public function testNotify()
    {
        
    }

    /**
     * Test de la méthode __call($name, $arg)
     */
    public function test__call()
    {
        
    }

    /**
     * Test de la méthode set_debug($debug)
     */
    public function testSet_debug()
    {
        
    }

}

/**
 * Mock pour la classe Kernel
 */
class MockKernel extends \BFW\Kernel
{
    /**
     * Accesseur get
     */
    public function __get($name) {return $this->$name;}
}

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
        $this->mock  = new MockKernel();
    }

    /**
     * Test de la méthode attach($observer)
     */
    public function testAttach()
    {
        $observer = new MockKernelSplObserver;
        
        $this->mock->attach($observer);
        $this->object($this->mock->observers[0])->isIdenticalTo($observer);
    }

    /**
     * Test de la méthode attachOther($observer)
     */
    public function testAttachOther()
    {
        $observer = new MockKernelObserver;
        
        $this->mock->attachOther($observer);
        $this->object($this->mock->observers[0])->isIdenticalTo($observer);
    }

    /**
     * Test de la méthode detach($observer)
     */
    public function testDetach()
    {
        $observerA = new MockKernelSplObserver;
        $observerB = new MockKernelSplObserver;
        $observerC = new MockKernelSplObserver;
        
        $this->mock->attach($observerA);
        $this->object($this->mock->observers[0])->isIdenticalTo($observerA);
        $this->mock->attach($observerB);
        $this->object($this->mock->observers[1])->isIdenticalTo($observerB);
        $this->mock->attach($observerC);
        $this->object($this->mock->observers[2])->isIdenticalTo($observerC);
        
        $this->mock->detach($observerB);
        
        $this->object($this->mock->observers[0])->isIdenticalTo($observerA);
        $this->array($this->mock->observers)->notHasKey(1);
        $this->object($this->mock->observers[2])->isIdenticalTo($observerC);
    }

    /**
     * Test de la méthode detachOther($observer)
     */
    public function testDetachOther()
    {
        $observerA = new MockKernelObserver;
        $observerB = new MockKernelObserver;
        $observerC = new MockKernelObserver;
        
        $this->mock->attachOther($observerA);
        $this->object($this->mock->observers[0])->isIdenticalTo($observerA);
        $this->mock->attachOther($observerB);
        $this->object($this->mock->observers[1])->isIdenticalTo($observerB);
        $this->mock->attachOther($observerC);
        $this->object($this->mock->observers[2])->isIdenticalTo($observerC);
        
        $this->mock->detachOther($observerB);
        
        $this->object($this->mock->observers[0])->isIdenticalTo($observerA);
        $this->array($this->mock->observers)->notHasKey(1);
        $this->object($this->mock->observers[2])->isIdenticalTo($observerC);
    }

    /**
     * Test de la méthode notifyObserver($action)
     */
    public function testNotifyObserver()
    {
        $observer = new MockKernelObserver;
        $this->mock->attachOther($observer);
        
        $this->mock->notifyObserver('test');
        $this->variable($this->mock->notify_action)->isNull();
    }

    /**
     * Test de la méthode notifyAction($action)
     */
    public function testNotifyAction()
    {
        $this->object($this->mock->notifyAction('test'))->isIdenticalTo($this->mock);
        $this->string($this->mock->notify_action)->isEqualTo('test');
    }

    /**
     * Test de la méthode notify()
     */
    public function testNotify()
    {
        //Test avec une action à envoyer : appel updateWithAction
        $this->mock->notifyAction('test');
        $this->mock->notify();
        
        //Test sans action : appel update()
        $observer = new MockKernelSplObserver;
        $this->mock->attach($observer);
        $this->mock->notify();
    }

    /**
     * Test de la méthode setDebug($debug)
     */
    public function testSetDebug()
    {
        $this->mock->setDebug(true);
        
        $this->boolean($this->mock->debug)->isTrue();
        $this->integer(error_reporting())->isEqualTo(E_ALL);
        $this->string(ini_get('display_errors'))->isEqualTo('On');
        $this->string(ini_get('html_errors'))->isEqualTo('1');
        
        
        $this->mock->setDebug(false);
        
        $this->boolean($this->mock->debug)->isFalse();
        $this->integer(error_reporting())->isEqualTo(0);
    }

    /**
     * Test de la méthode getDebug()
     */
    public function testGetDebug()
    {
        $this->boolean($this->mock->getDebug())->isFalse();
        
        $this->mock->setDebug(false);
        $this->boolean($this->mock->getDebug())->isFalse();
        
        $this->mock->setDebug(true);
        $this->boolean($this->mock->debug)->isTrue();
    }

}

/**
 * Mock pour les observers
 */
class MockKernelSplObserver implements \SplObserver
{
    /**
     * Méthode par défaut appelé lorsque l'observer se déclanche
     * 
     * @param SplSubject $subject Le sujet déclanchant l'observer
     */
    public function update(\SplSubject $subject)
    {
        
    }
}

/**
 * Mock pour les observers
 */
class MockKernelObserver implements \SplObserver
{
    /**
     * Méthode par défaut appelé lorsque l'observer se déclanche
     * 
     * @param SplSubject $subject Le sujet déclanchant l'observer
     */
    public function update(\SplSubject $subject)
    {
        
    }
    
    /**
     * L'action à effectuer quand l'observer est déclanché
     * 
     * @param BFW\Kernel $subject Le sujet observant
     * @param array      $action  Les actions à effectuer
     */
    public function updateWithAction($subject, $action)
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

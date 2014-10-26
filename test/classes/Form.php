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
        $this->mock  = new MockForm();
    }

    /**
     * Test du constructeur : Form($idForm=)
     */
    public function testForm()
    {
        $this->mock = new MockForm();
        $this->variable($this->mock->idForm)->isNull();
        $this->object($this->mock->_kernel)->isInstanceOf('\BFW\Kernel');
    }

    /**
     * Test de la méthode setIdForm($idForm)
     */
    public function testSetIdForm()
    {
        $this->mock->setIdForm('test');
        $this->string($this->mock->idForm)->isEqualTo('test');
    }

    /**
     * Test de la méthode tokenCreate()
     */
    public function testTokenCreate()
    {
        $_SESSION = array();
        
        $this->mock->setIdForm('test');
        $token = $this->mock->tokenCreate();
        
        $this->variable($_SESSION['token']['test'])->isNotNull();
        $this->variable($_SESSION['token']['test']['token'])->isNotNull();
        $this->variable($_SESSION['token']['test']['date'])->isNotNull();
        
        $this->string($token)->isEqualTo($_SESSION['token']['test']['token']);
        
        $this->exception(function()
        {
            $mock = new MockForm();
            $mock->tokenCreate();
        })->message->contains('Form name is undefined.');
    }

    /**
     * Test de la méthode tokenVerif()
     */
    public function testTokenVerif()
    {
        $_SESSION = array();
        
        $this->mock->setIdForm('test');
        $token = $this->mock->tokenCreate();
        
        $_POST['token'] = $token;
        $this->boolean($this->mock->tokenVerif())->isTrue();
        
        
        //Test avec la limite des 15 minutes dépassé
        $token = $this->mock->tokenCreate();
        $_POST['token'] = $token;
        
        $date = $_SESSION['token']['test']['date'];
        $date = new \BFW\Date($date);
        $date->modify('-30 minutes');
        $_SESSION['token']['test']['date'] = $date->getDate();
        $this->boolean($this->mock->tokenVerif())->isFalse();
        
        
        //Test avec un mauvais token
        $token = $this->mock->tokenCreate();
        $_POST['token'] = 'test';
        $this->boolean($this->mock->tokenVerif())->isFalse();
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

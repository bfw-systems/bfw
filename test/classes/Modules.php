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
        $this->mock  = new MockModules();
    }

    /**
     * Test du constructeur : Modules()
     */
    public function testModules()
    {
        $this->mock  = new MockModules();
        $this->object($this->mock->_kernel)->isInstanceOf('\BFW\Kernel');
    }

    /**
     * Test de la méthode newMod($name, $params=array())
     */
    public function testNewMod()
    {
        $this->mock->newMod('test');
        
        $this->array($this->mock->modList['test'])->isEqualTo(array(
            'name' => 'test',
            'time' => modulesLoadTime_EndInit,
            'require' => array()
        ));
        
        $this->mock->newMod('test2', array(
            'require' => 'test',
            'time'    => modulesLoadTime_Visiteur
        ));
        $this->array($this->mock->modList['test2'])->isEqualTo(array(
            'name' => 'test2',
            'time' => modulesLoadTime_Visiteur,
            'require' => array('test')
        ));
        
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->newMod('test');
        })->message->contains('Le module test existe déjà.');
        
        $this->exception(function() use($mock)
        {
            $mock->newMod('testFail', 'test');
        })->message->contains('Les options du module testFail doivent être déclarer sous la forme d\'un array.');
    }

    /**
     * Test de la méthode exists($name)
     */
    public function testExists()
    {
        $this->mock->newMod('test');
        $this->boolean($this->mock->exists('test'))->isTrue();
        $this->boolean($this->mock->exists('test2'))->isFalse();
    }

    /**
     * Test de la méthode isLoad($name)
     */
    public function testIsLoad()
    {
        $this->mock->newMod('test');
        $this->boolean($this->mock->isLoad('test2'))->isFalse();
    }

    /**
     * Test de la méthode addPath($name, $path)
     */
    public function testAddPath()
    {
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->addPath('test', 'path');
        })->message->contains('Le module test n\'existe pas.');
        
        $this->mock->newMod('test');
        $mock->addPath('test', 'path');
        $this->string($this->mock->modList['test']['path'])->isEqualTo('path');
    }

    /**
     * Test de la méthode listToLoad($timeToLoad)
     */
    public function testListToLoad()
    {
        $this->array($this->mock->listToLoad('time'))->isEqualTo(array());
        $this->mock->newMod('test');
        
        $this->array($this->mock->listToLoad(modulesLoadTime_EndInit))->isEqualTo(array('test'));
    }

    /**
     * Test de la méthode modToLoad($mod, &$arrayToLoad)
     */
    public function testModToLoad()
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

    /**
     * Test de la méthode modToLoad($mod, &$arrayToLoad)
     */
    public function modToLoad($mod, &$arrayToLoad)
    {
        return parent::modToLoad($mod, $arrayToLoad);
    }

}

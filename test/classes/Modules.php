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
            'require' => array(),
            'runFile' => 'inclus.php',
            'priority' => 0
        ));
        
        $this->mock->newMod('test2', array(
            'require' => 'test',
            'time'    => modulesLoadTime_Visiteur,
            'priority' => 1
        ));
        $this->array($this->mock->modList['test2'])->isEqualTo(array(
            'name' => 'test2',
            'time' => modulesLoadTime_Visiteur,
            'require' => array('test'),
            'runFile' => 'inclus.php',
            'priority' => 1
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
        $this->boolean($this->mock->isLoad('test'))->isFalse();
        $this->boolean($this->mock->isLoad('test2'))->isFalse();
        
        $this->mock->loaded('test');
        $this->boolean($this->mock->isLoad('test'))->isTrue();
    }

    /**
     * Test de la méthode loaded($name)
     */
    public function testLoaded()
    {
        $this->mock->newMod('test');
        $this->mock->loaded('test');
        
        $this->array($this->mock->modLoad)->isEqualTo(array(0 => 'test'));
        
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->loaded('test2');
        })->message->contains('Module test2 not exists.');
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
        $arrayToLoad = array();
        $this->mock->newMod('test');
        
        //Test d'un module sans dépendance et non chargé
        $testModInfos = $this->mock->modList['test'];
        $this->boolean($this->mock->modToLoad($testModInfos, $arrayToLoad))->isTrue();
        
        //Test d'un module sans dépendance et déjà chargé
        $testModInfos = $this->mock->modList['test'];
        $this->boolean($this->mock->modToLoad($testModInfos, $arrayToLoad))->isTrue();
        
        //Test d'un module avec dépendance sans erreur
        $this->mock->newMod('test2', array('require' => 'test'));
        $test2ModInfos = $this->mock->modList['test'];
        $this->boolean($this->mock->modToLoad($test2ModInfos, $arrayToLoad))->isTrue();
        
        $mock = &$this->mock;
        
        //Test d'un module avec une dépendance qui n'existe pas dans la liste des modules
        $this->mock->newMod('test3', array('require' => 'notExist'));
        $this->exception(function() use($mock, $arrayToLoad)
        {
            $test3ModInfos = $mock->modList['test3'];
            $mock->modToLoad($test3ModInfos, $arrayToLoad);
        })->message->contains('La dépendance notExist du module test3 n\'a pas été trouvé.');
        
        //Test d'un module avec une dépendance qui existe mais qui n'est pas chargé.
        $this->mock->newMod('test4', array('time' => modulesLoadTime_EndInit));
        $this->mock->newMod('test5', array(
            'time'    => modulesLoadTime_Visiteur,
            'require' => 'test4'
        ));
    }

    /**
     * Test de la méthode listNotLoad($regen=false)
     */
    public function testListNotLoad()
    {
        //Aucun module existe, donc la liste est vide et automatiquement généré
        $this->array($this->mock->listNotLoad())->isEqualTo(array());
        
        $this->mock->newMod('test');
        
        //La liste est toujours vide car non regénéré
        $this->array($this->mock->listNotLoad())->isEqualTo(array());
        
        //La liste est regénéré
        $this->array($this->mock->listNotLoad(true))->isEqualTo(array(
            'test' => array(
                'name'    => 'test',
                'time'    => 'endInit',
                'require' => array(),
                'runFile' => 'inclus.php',
                'priority' => 0
            )
        ));
    }

    /**
     * Test de la méthode isModulesNotLoad()
     */
    public function testIsModulesNotLoad()
    {
        $this->boolean($this->mock->isModulesNotLoad())->isFalse();
        
        $this->mock->newMod('test');
        $this->boolean($this->mock->isModulesNotLoad())->isFalse(); //Cache
        
        $this->mock->listNotLoad(true); //Kill le cache
        $this->boolean($this->mock->isModulesNotLoad())->isTrue();
    }

    /**
     * Test de la méthode getModuleInfos($name)
     */
    public function testGetModuleInfos()
    {
        $this->mock->newMod('test');
        $this->array($this->mock->getModuleInfos('test'))->isEqualTo(array(
            'name'    => 'test',
            'time'    => 'endInit',
            'require' => array(),
            'runFile' => 'inclus.php',
            'priority' => 0
        ));
        
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->getModuleInfos('test2');
        })->message->contains('Le module test2 n\'existe pas.');
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
     * Test de la méthode modToLoad(&$mod, &$arrayToLoad, $waitToLoad)
     */
    public function modToLoad(&$mod, &$arrayToLoad, $waitToLoad=array())
    {
        return parent::modToLoad($mod, $arrayToLoad, $waitToLoad);
    }
}

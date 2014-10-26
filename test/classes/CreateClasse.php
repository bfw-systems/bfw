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
     * 
     * @param string $testMethod
     */
    public function beforeTestMethod($testMethod)
    {
        $this->class = new \BFW\CreateClasse('test');
        $this->mock  = new MockCreateClasse('test');
    }

    /**
     * Test du constructeur : CreateClasse($nom, $options=array())
     */
    public function testCreateClasse()
    {
        $this->string($this->mock->indente)->isEqualTo('    ');
        $this->string($this->mock->extends)->isEqualTo('');
        $this->array($this->mock->implements)->isEqualTo(array());
        
        $mock = new MockCreateClasse('test', array(
            'indente'    => '  ',
            'extends'    => 'myClass',
            'implements' => array('IMyClass')
        ));
        
        $this->string($mock->indente)->isEqualTo('  ');
        $this->string($mock->extends)->isEqualTo('myClass');
        $this->array($mock->implements)->isEqualTo(array('IMyClass'));
    }

    /**
     * Test de la méthode getFile()
     */
    public function testGetFile()
    {
        $this->string($this->class->getFile())->isEqualTo('');
    }

    /**
     * Test de la méthode createAttribut($nom, $opt=array())
     */
    public function testCreateAttribut()
    {
        $this->boolean($this->mock->createAttribut('test'))->isTrue();
        $this->array($this->mock->attributs)->isEqualTo(array('test'));
        $this->array($this->mock->attributs_porter)->isEqualTo(array('protected'));
        $this->array($this->mock->attributs_option)->isEqualTo(array(array(
            'porter' => 'protected',
            'get'    => 1,
            'set'    => 1,
        )));
        $this->array($this->mock->get)->isEqualTo(array('test'));
        $this->array($this->mock->set)->isEqualTo(array('test'));
        
        
        $this->boolean($this->mock->createAttribut('test2', array('porter' => 'public', 'get' => 0, 'set' => 0)))->isTrue();
        $this->array($this->mock->attributs)->isEqualTo(array('test', 'test2'));
        $this->array($this->mock->attributs_porter)->isEqualTo(array('protected', 'public'));
        $this->array($this->mock->attributs_option)->isEqualTo(array(
            array(
                'porter' => 'protected',
                'get'    => 1,
                'set'    => 1,
            ),
            array(
                'porter' => 'public',
                'get'    => 0,
                'set'    => 0
            )
        ));
        $this->array($this->mock->get)->isEqualTo(array('test'));
        $this->array($this->mock->set)->isEqualTo(array('test'));
        
        
        $this->boolean($this->mock->createAttribut('test'))->isFalse();
    }

    /**
     * Test de la méthode createMethode($nom, $porter='private')
     */
    public function testCreateMethode()
    {
        $this->boolean($this->mock->createMethode('test'))->isTrue();
        $this->array($this->mock->methode)->isEqualTo(array('test'));
        $this->array($this->mock->methode_porter)->isEqualTo(array('protected'));
        
        $this->boolean($this->mock->createMethode('test2', 'public'))->isTrue();
        $this->array($this->mock->methode)->isEqualTo(array('test', 'test2'));
        $this->array($this->mock->methode_porter)->isEqualTo(array('protected', 'public'));
        
        $this->boolean($this->mock->createMethode('test'))->isFalse();
    }

    /**
     * Test de la méthode genereAttribut($key)
     */
    public function testGenereAttribut()
    {
        $this->mock->createAttribut('test');
        $this->mock->genereAttribut(0);
        
        $this->string($this->mock->file)->isEqualTo(
            '    /**'."\n".
            '     * @var $test : Ma description.'."\n".
            '     */'."\n".
            '    protected $test;'."\n\n"
        );
        
        $this->beforeTestMethod('');
        $this->mock->createAttribut('test', array('default' => 'true'));
        $this->mock->genereAttribut(0);
        
        $this->string($this->mock->file)->isEqualTo(
            '    /**'."\n".
            '     * @var $test : Ma description. Par défaut à true.'."\n".
            '     */'."\n".
            '    protected $test = true;'."\n\n"
        );
        
        $this->beforeTestMethod('');
        $this->mock->createAttribut('test', array('default' => 'monTest', 'default_string' => true));
        $this->mock->genereAttribut(0);
        
        $this->string($this->mock->file)->isEqualTo(
            '    /**'."\n".
            '     * @var $test : Ma description. Par défaut à \'monTest\'.'."\n".
            '     */'."\n".
            '    protected $test = \'monTest\';'."\n\n"
        );
    }

    /**
     * Test de la méthode genereGet($key)
     */
    public function testGenereGet()
    {
        $this->mock->createAttribut('test');
        $this->mock->genereGet(0);
        
        $this->string($this->mock->file)->isEqualTo(
            '    /**'."\n".
            '     * Accesseur get vers test'."\n".
            '     *'."\n".
            '     * @return mixed : La valeur de test'."\n".
            '     */'."\n".
            '    public function get_test() {return $this->test;}'."\n\n"
        );
    }

    /**
     * Test de la méthode genereSet($key)
     */
    public function testGenereSet()
    {
        $this->mock->createAttribut('test');
        $this->mock->genereSet(0);
        
        $this->string($this->mock->file)->isEqualTo(
            '    /**'."\n".
            '     * Accesseur set vers test'."\n".
            '     *'."\n".
            '     * @param mixed : La nouvelle valeur de test'."\n".
            '     *'."\n".
            '     * @return bool : True si réussi, False sinon.'."\n".
            '     */'."\n".
            '    public function set_test($data) {return ($this->test = $data) ? true : false;}'."\n\n"
        );
    }

    /**
     * Test de la méthode genereMethode($key)
     */
    public function testGenereMethode()
    {
        $this->mock->createMethode('test');
        $this->mock->genereMethode(0);
        
        $this->string($this->mock->file)->isEqualTo(
            '    /**'."\n".
            '     * Description de ma méthode.'."\n".
            '     */'."\n".
            '    protected function test() {}'."\n"
        );
    }

    /**
     * Test de la méthode genere()
     */
    public function testGenere()
    {
        $this->mock  = new MockCreateClasse('test', array(
            'extends'    => 'myClass',
            'implements' => array('IMyClass')
        ));
        
        $this->mock->createAttribut('test');
        $this->mock->createMethode('test');
        
        $this->mock->genere();
        $this->string($this->mock->file)->isEqualTo(
            '<?php'."\n".
            '/**'."\n".
            ' * Ma description du fichier'."\n".
            ' * @author me'."\n".
            ' * @version 1.0'."\n".
            ' */'."\n".
            ''."\n".
            '/**'."\n".
            ' * La description de ma classe'."\n".
            ' * @package MonProjet'."\n".
            ' */'."\n".
            'class test extends myClass implements IMyClass'."\n".
            '{'."\n".
            '    /**'."\n".
            '     * @var $test : Ma description.'."\n".
            '     */'."\n".
            '    protected $test;'."\n".
            ''."\n".
            ''."\n".
            '    /**'."\n".
            '     * Constructeur de la classe'."\n".
            '     */'."\n".
            '    public function __construct() {}'."\n".
            ''."\n".
            '    /**'."\n".
            '     * Accesseur get vers test'."\n".
            '     *'."\n".
            '     * @return mixed : La valeur de test'."\n".
            '     */'."\n".
            '    public function get_test() {return $this->test;}'."\n".
            ''."\n".
            '    /**'."\n".
            '     * Accesseur set vers test'."\n".
            '     *'."\n".
            '     * @param mixed : La nouvelle valeur de test'."\n".
            '     *'."\n".
            '     * @return bool : True si réussi, False sinon.'."\n".
            '     */'."\n".
            '    public function set_test($data) {return ($this->test = $data) ? true : false;}'."\n".
            ''."\n".
            '    /**'."\n".
            '     * Description de ma méthode.'."\n".
            '     */'."\n".
            '    protected function test() {}'."\n".
            '}'."\n".
            '?>'
        );
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

    /**
     * Test de la méthode genereAttribut($key)
     * 
     * @param integer $key
     */
    public function genereAttribut($key)
    {
        return parent::genereAttribut($key);
    }

    /**
     * Test de la méthode genereGet($key)
     * 
     * @param integer $key
     */
    public function genereGet($key)
    {
        return parent::genereGet($key);
    }

    /**
     * Test de la méthode genereSet($key)
     * 
     * @param integer $key
     */
    public function genereSet($key)
    {
        return parent::genereSet($key);
    }

    /**
     * Test de la méthode genereMethode($key)
     * 
     * @param integer $key
     */
    public function genereMethode($key)
    {
        return parent::genereMethode($key);
    }

}

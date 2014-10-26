<?php
/**
 * Fichier de test pour une class
 */

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../common.php');

/**
 * Test de la class Date
 */
class Date extends atoum
{
    /**
     * @var $class : Instance de la class Date
     */
    protected $class;

    /**
     * @var $mock : Instance du mock pour la class Date
     */
    protected $mock;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        //$this->class = new \BFW\Date();
        //$this->mock  = new MockDate();
    }

    /**
     * Test de la méthode getDate()
     */
    public function testGetDate()
    {
        
    }

    /**
     * Test de la méthode getAnnee()
     */
    public function testGetAnnee()
    {
        
    }

    /**
     * Test de la méthode getMois()
     */
    public function testGetMois()
    {
        
    }

    /**
     * Test de la méthode getJour()
     */
    public function testGetJour()
    {
        
    }

    /**
     * Test de la méthode getHeure()
     */
    public function testGetHeure()
    {
        
    }

    /**
     * Test de la méthode getMinute()
     */
    public function testGetMinute()
    {
        
    }

    /**
     * Test de la méthode getSeconde()
     */
    public function testGetSeconde()
    {
        
    }

    /**
     * Test de la méthode getZone()
     */
    public function testGetZone()
    {
        
    }

    /**
     * Test de la méthode MAJ_Attributes()
     */
    public function testMAJ_Attributes()
    {
        
    }

    /**
     * Test de la méthode getSql($decoupe=)
     */
    public function testGetSql()
    {
        
    }

    /**
     * Test de la méthode setZone($NewZone)
     */
    public function testSetZone()
    {
        
    }

    /**
     * Test de la méthode lst_TimeZone()
     */
    public function testLst_TimeZone()
    {
        
    }

    /**
     * Test de la méthode lst_TimeZoneContinent()
     */
    public function testLst_TimeZoneContinent()
    {
        
    }

    /**
     * Test de la méthode lst_TimeZonePays($continent)
     */
    public function testLst_TimeZonePays()
    {
        
    }

    /**
     * Test de la méthode aff_simple($tout=1, $minus=)
     */
    public function testAff_simple()
    {
        
    }

}

/**
 * Mock pour la classe Date
 */
class MockDate extends \BFW\Date
{
    /**
     * Accesseur get
     */
    public function __get($name) {return $this->$name;}

}

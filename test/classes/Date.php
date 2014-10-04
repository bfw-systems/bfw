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
     * Test du constructeur : Date($date=now)
     */
    public function testDate()
    {
        
    }

    /**
     * Test de la méthode modify($cond)
     */
    public function testModify()
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

    /**
     * Test de la méthode __wakeup()
     */
    public function test__wakeup()
    {
        
    }

    /**
     * Test de la méthode __set_state()
     */
    public function test__set_state()
    {
        
    }

    /**
     * Test de la méthode createFromFormat($format, $time, $object)
     */
    public function testCreateFromFormat()
    {
        
    }

    /**
     * Test de la méthode getLastErrors()
     */
    public function testGetLastErrors()
    {
        
    }

    /**
     * Test de la méthode format($format)
     */
    public function testFormat()
    {
        
    }

    /**
     * Test de la méthode add($interval)
     */
    public function testAdd()
    {
        
    }

    /**
     * Test de la méthode sub($interval)
     */
    public function testSub()
    {
        
    }

    /**
     * Test de la méthode getTimezone()
     */
    public function testGetTimezone()
    {
        
    }

    /**
     * Test de la méthode setTimezone($timezone)
     */
    public function testSetTimezone()
    {
        
    }

    /**
     * Test de la méthode getOffset()
     */
    public function testGetOffset()
    {
        
    }

    /**
     * Test de la méthode setTime($hour, $minute, $second)
     */
    public function testSetTime()
    {
        
    }

    /**
     * Test de la méthode setDate($year, $month, $day)
     */
    public function testSetDate()
    {
        
    }

    /**
     * Test de la méthode setISODate($year, $week, $day)
     */
    public function testSetISODate()
    {
        
    }

    /**
     * Test de la méthode setTimestamp($unixtimestamp)
     */
    public function testSetTimestamp()
    {
        
    }

    /**
     * Test de la méthode getTimestamp()
     */
    public function testGetTimestamp()
    {
        
    }

    /**
     * Test de la méthode diff($object, $absolute)
     */
    public function testDiff()
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

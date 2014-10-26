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
        $this->mock = new MockDate('2014-10-26 15:01:00+01:00');
    }

    /**
     * Test de la méthode getDate()
     */
    public function testGetDate()
    {
        $this->string($this->mock->getDate())->isEqualTo('2014-10-26 15:01:00+0100');
    }

    /**
     * Test de la méthode getAnnee()
     */
    public function testGetAnnee()
    {
        $this->string($this->mock->getAnnee())->isEqualTo('2014');
    }

    /**
     * Test de la méthode getMois()
     */
    public function testGetMois()
    {
        $this->string($this->mock->getMois())->isEqualTo('10');
    }

    /**
     * Test de la méthode getJour()
     */
    public function testGetJour()
    {
        $this->string($this->mock->getJour())->isEqualTo('26');
    }

    /**
     * Test de la méthode getHeure()
     */
    public function testGetHeure()
    {
        $this->string($this->mock->getHeure())->isEqualTo('15');
    }

    /**
     * Test de la méthode getMinute()
     */
    public function testGetMinute()
    {
        $this->string($this->mock->getMinute())->isEqualTo('01');
    }

    /**
     * Test de la méthode getSeconde()
     */
    public function testGetSeconde()
    {
        $this->string($this->mock->getSeconde())->isEqualTo('00');
    }

    /**
     * Test de la méthode getZone()
     */
    public function testGetZone()
    {
        $this->string($this->mock->getZone())->isEqualTo('+01:00');
    }

    /**
     * Test du constructeur : Date($date="now")
     */
    public function testDate()
    {
        $this->mock = new MockDate;
        $date = new \DateTime;
        $this->string($this->mock->getDate())->isEqualTo($date->format('Y-m-d H:i:sO'));
        
        $this->mock = new MockDate('2014-10-26 15:01:00+01:00');
        $this->string($this->mock->getDate())->isEqualTo('2014-10-26 15:01:00+0100');
        
        $this->mock = new MockDate('2014-10-26 15:01:00+01');
        $this->string($this->mock->getDate())->isEqualTo('2014-10-26 15:01:00+0100');
    }

    /**
     * Test de la méthode modify($cond)
     */
    public function testModify()
    {
        $this->mock->modify('+1 day');
        $this->string($this->mock->getDate())->isEqualTo('2014-10-27 15:01:00+0100');
        
        $this->mock = new MockDate('2014-10-26 15:01:00+01:00');
        $this->mock->modify('+1 jour');
        $this->string($this->mock->getDate())->isEqualTo('2014-10-27 15:01:00+0100');
        
        $mock = $this->mock;
        $this->exception(function() use($mock)
        {
            $mock->modify('+1 test');
        })->message->contains('Parameter test is unknown.');
    }

    /**
     * Test de la méthode getSql($decoupe=)
     */
    public function testGetSql()
    {
        $this->string($this->mock->getSql())->isEqualTo('2014-10-26 15:01:00');
        $this->array($this->mock->getSql(true))->isEqualTo(array('2014-10-26', '15:01:00'));
    }

    /**
     * Test de la méthode setZone($NewZone)
     */
    public function testSetZone()
    {
        $this->mock->setZone('Europe/Paris');
        $dateTimeZone = new \DateTimeZone('Europe/Paris');
        $this->object($this->mock->getTimezone())->isCloneOf($dateTimeZone);
    }

    /**
     * Test de la méthode lst_TimeZone()
     */
    public function testLst_TimeZone()
    {
        $dateTimeZone = new \DateTimeZone('Europe/Paris');
        $lstTimeZone  = $dateTimeZone->listIdentifiers();
        
        $this->array($this->mock->lst_TimeZone())->isEqualTo($lstTimeZone); 
    }

    /**
     * Test de la méthode lst_TimeZoneContinent()
     */
    public function testLst_TimeZoneContinent()
    {
        $this->array($this->mock->lst_TimeZoneContinent())->isEqualTo(array(
            'africa', 
            'america', 
            'antartica', 
            'arctic', 
            'asia', 
            'atlantic', 
            'australia', 
            'europe', 
            'indian', 
            'pacific'
        ));
    }

    /**
     * Test de la méthode lst_TimeZonePays($continent)
     */
    public function testLst_TimeZonePays()
    {
        $lstAntarticaTimeZone = array(
            'Antarctica/Casey',
            'Antarctica/Davis',
            'Antarctica/DumontDUrville',
            'Antarctica/Macquarie',
            'Antarctica/Mawson',
            'Antarctica/McMurdo',
            'Antarctica/Palmer',
            'Antarctica/Rothera',
            'Antarctica/South_Pole',
            'Antarctica/Syowa',
            'Antarctica/Troll',
            'Antarctica/Vostok'
        );
        
        
        $dateTimeZone = new \DateTimeZone('Europe/Paris');
        $lstTimeZone  = $dateTimeZone->listIdentifiers();
        
        $lstTimeZoneTest = array();
        foreach($lstAntarticaTimeZone as $timeZone)
        {
            if(in_array($timeZone, $lstTimeZone))
            {
                $lstTimeZoneTest[] = $timeZone;
            }
        }
        
        $this->array($this->mock->lst_TimeZonePays('Antarctica'))->isEqualTo($lstTimeZoneTest);
    }

    /**
     * Test de la méthode aff_simple($tout=1, $minus=)
     */
    public function testAff_simple()
    {
        //echo $this->mock->aff_simple();
        
        $this->mock = new MockDate;
        $this->string($this->mock->aff_simple())->isEqualTo('Maintenant');
        
        $this->mock->modify('-30 second');
        $this->string($this->mock->aff_simple())->isEqualTo('Il y a 30s');
        
        $this->mock->modify('-50 minute');
        $this->string($this->mock->aff_simple())->isEqualTo('Il y a 50min');
        
        $this->mock->modify('-10 minute');
        $this->string($this->mock->aff_simple())->isEqualTo('Il y a 01h');
        
        $this->mock->modify('-10 minute');
        $this->string($this->mock->aff_simple())->isEqualTo('Il y a 01h');
        
        $this->mock->modify('-20 hour');
        $this->string($this->mock->aff_simple())->isEqualTo('Hier à '.$this->mock->format('H:i'));
        
        $this->mock->modify('-14 day');
        $this->string($this->mock->aff_simple())->isEqualTo('Le '.$this->mock->format('d/m').' à '.$this->mock->format('H:i'));
        
        $this->mock->modify('-1 year');
        $this->string($this->mock->aff_simple())->isEqualTo('Le '.$this->mock->format('d/m/Y').' à '.$this->mock->format('H:i'));
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

<?php

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Dates extends atoum
{
    /**
     * @var $mock : Instance du mock pour la class
     */
    protected $mock;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        $this->mock = new MockDates;
    }
    
    public function testGetHumainReadableI18n()
    {
        $this->assert('test getHumainReadableI18n')
            ->array(MockDates::getHumainReadableI18n())
                ->isEqualTo([
                    'now'       => 'now',
                    'since'     => 'since',
                    'yesterday' => 'yesterday',
                    'the'       => 'the',
                    'at'        => 'at'
                ]);
    }
    
    public function testSetHumainReadableI18nKey()
    {
        $key   = 'now';
        $value = 'maintenant';
        
        $this->assert('test setHumainReadableI18nKey')
            ->given(MockDates::setHumainReadableI18nKey($key, $value))
            ->array(MockDates::getHumainReadableI18n())
                ->isEqualTo([
                    'now'       => 'maintenant',
                    'since'     => 'since',
                    'yesterday' => 'yesterday',
                    'the'       => 'the',
                    'at'        => 'at'
                ]);
        
        $key   = 'nox';
        $value = 'maintenant';
        
        $this->assert('test setHumainReadableI18nKey new key')
            ->given(MockDates::setHumainReadableI18nKey($key, $value))
            ->array(MockDates::getHumainReadableI18n())
                ->isEqualTo([
                    'now'       => 'maintenant',
                    'since'     => 'since',
                    'yesterday' => 'yesterday',
                    'the'       => 'the',
                    'at'        => 'at',
                    'nox'       => 'maintenant'
                ]);
    }
    
    public function testSetHumainReadableI18n()
    {
        $newValue = [
            'test' => 'test',
            'test2' => 'test2'
        ];
        
        $this->assert('test setHumainReadableI18n')
            ->given(MockDates::setHumainReadableI18n($newValue))
            ->array(MockDates::getHumainReadableI18n())
                ->isEqualTo($newValue);
    }
    
    public function testGetHumainReadableFormats()
    {
        $this->assert('test getHumainReadableFormats')
            ->array(MockDates::getHumainReadableFormats())
                ->isEqualTo([
                    'dateSameYear'      => 'm-d',
                    'dateDifferentYear' => 'Y-m-d',
                    'time'              => 'H:i'
                ]);
    }
    
    public function testSetHumainReadableFormatsKey()
    {
        $key   = 'time';
        $value = 'H:i:s';
        
        $this->assert('test setHumainReadableFormatsKey')
            ->given(MockDates::setHumainReadableFormatsKey($key, $value))
            ->array(MockDates::getHumainReadableFormats())
                ->isEqualTo([
                    'dateSameYear'      => 'm-d',
                    'dateDifferentYear' => 'Y-m-d',
                    'time'              => 'H:i:s'
                ]);
        
        $key   = 'gmt';
        $value = 'O';
        
        $this->assert('test setHumainReadableFormatsKey new key')
            ->given(MockDates::setHumainReadableFormatsKey($key, $value))
            ->array(MockDates::getHumainReadableFormats())
                ->isEqualTo([
                    'dateSameYear'      => 'm-d',
                    'dateDifferentYear' => 'Y-m-d',
                    'time'              => 'H:i:s',
                    'gmt'               => 'O'
                ]);
    }
    
    public function testSetHumainReadableFormats()
    {
        $newValue = [
            'test' => 'test',
            'test2' => 'test2'
        ];
        
        $this->assert('test setHumainReadableFormats')
            ->given(MockDates::setHumainReadableFormats($newValue))
            ->array(MockDates::getHumainReadableFormats())
                ->isEqualTo($newValue);
    }
    
    public function testGetDate()
    {
        $this->assert('test getDate')
            ->string($this->mock->getDate())
                ->isEqualTo($this->mock->format('Y-m-d H:i:sO'));
    }
    
    public function testGetYear()
    {
        $this->assert('test getYear')
            ->integer($this->mock->getYear())
                ->isEqualTo((int) $this->mock->format('Y'));
    }
    
    public function testGetMonth()
    {
        $this->assert('test getMonth')
            ->integer($this->mock->getMonth())
                ->isEqualTo((int) $this->mock->format('m'));
    }
    
    public function testGetDay()
    {
        $this->assert('test getDay')
            ->integer($this->mock->getDay())
                ->isEqualTo((int) $this->mock->format('d'));
    }
    
    public function testGetHour()
    {
        $this->assert('test getHour')
            ->integer($this->mock->getHour())
                ->isEqualTo((int) $this->mock->format('H'));
    }
    
    public function testGetMinute()
    {
        $this->assert('test getMinute')
            ->integer($this->mock->getMinute())
                ->isEqualTo((int) $this->mock->format('i'));
    }
    
    public function testGetSecond()
    {
        $this->assert('test getSecond')
            ->integer($this->mock->getSecond())
                ->isEqualTo((int) $this->mock->format('s'));
    }
    
    public function testGetZone()
    {
        $this->assert('test getZone')
            ->string($this->mock->getZone())
                ->isEqualTo($this->mock->format('P'));
    }
    
    public function testModify()
    {
        $dt = new \DateTime;
        $dt->modify('+1 year');
        $dtFormat = $dt->format('Y-m-d');
        
        $this->assert('test modify original DateTime')
            ->given($this->mock->modify('+1 year'))
            ->string($this->mock->format('Y-m-d'))
                ->isEqualTo($dtFormat);
        
        $dt->modify('+1 year');
        $dtFormat = $dt->format('Y-m-d');
        
        $this->assert('test modify Dates keywords')
            ->given($this->mock->modify('+1 an'))
            ->string($this->mock->format('Y-m-d'))
                ->isEqualTo($dtFormat);
        
        $mock = $this->mock;
        $this->assert('test modify bad pattern')
            ->exception(function() use ($mock) {
                $mock->modify('year -1');
            })
            ->hasMessage('Dates::modify pattern not match.');
            
        $this->assert('test modify unknown keyword')
            ->exception(function() use ($mock) {
                $mock->modify('+1 annees');
            })
            ->hasMessage('Dates::modify Parameter annees is unknown.');
    }
    
    public function testGetSqlFormat()
    {
        $this->assert('test getSqlFormat string return')
            ->string($this->mock->getSqlFormat())
                ->isEqualTo($this->mock->format('Y-m-d H:i:s'));
        
        $this->assert('test getSqlFormat array return')
            ->array($this->mock->getSqlFormat(true))
                ->isEqualTo([
                    $this->mock->format('Y-m-d'),
                    $this->mock->format('H:i:s')
                ]);
    }
    
    public function testLstTimeZone()
    {
        $dateTimeZone = new \DateTimeZone('Europe/Paris');
        $lstTimeZone  = $dateTimeZone->listIdentifiers();
        
        $this->assert('test lstTimeZone')
            ->array($this->mock->lstTimeZone())
                ->isEqualTo($lstTimeZone);
    }
    
    public function testLstTimeZoneContinent()
    {
        $this->assert('test lstTimeZoneContinent')
            ->array($this->mock->lstTimeZoneContinent())
                ->isEqualTo([
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
                ]);
    }
    
    public function testLstTimeZonePays()
    {
        //Some time zone has added with new version of php
        //And Antarctica is the time zone with lesser of values
        $lstAntarticaTimeZone = [
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
        ];
        
        $dateTimeZone = new \DateTimeZone('Europe/Paris');
        $lstTimeZone  = $dateTimeZone->listIdentifiers();
        
        //So we list all time zone,
        //and if it's on the antarctica list, we save it
        $lstTimeZoneTest = [];
        foreach ($lstAntarticaTimeZone as $timeZone) {
            if (in_array($timeZone, $lstTimeZone)) {
                $lstTimeZoneTest[] = $timeZone;
            }
        }
        
        $this->assert('test lstTimeZonePays')
            ->array($this->mock->lstTimeZonePays('Antarctica'))
                ->isEqualTo($lstTimeZoneTest);
    }
    
    public function testHumainReadable()
    {
        $hrFormat = MockDates::getHumainReadableFormats();
        
        /*
         * Disable because this test fail all, a "lag" return "since 1s"
        $this->assert('test humaineReadable : now')
            ->string($this->mock->humainReadable())
                ->isEqualTo($hrI18n['now']);
        */
        
        $this->assert('test humainReadable : Since')
            /*->given($this->mock->modify('-10 second'))
            ->string($this->mock->humainReadable())
                ->isEqualTo('Since 10s')
            ->string($this->mock->humainReadable(false, true))
                ->isEqualTo('since 10s')*/
            ->given($this->mock->modify('-30 minute'))
            ->string($this->mock->humainReadable())
                ->isEqualTo('since 30min')
            ->string($this->mock->humainReadable(false))
                ->isEqualTo('since 30min')
            ->given($this->mock->modify('-1 hour'))
            ->string($this->mock->humainReadable())
                ->isEqualTo('since 1h')
            ->string($this->mock->humainReadable(false))
                ->isEqualTo('since 1h');
        
        $this->assert('test humainReadable : Yesterday')
            ->given($this->mock->modify('-25 hour'))
            ->given($yesterdayFormat = $this->mock->format($hrFormat['time']))
            ->string($this->mock->humainReadable())
                ->isEqualTo('yesterday at '.$yesterdayFormat)
            ->string($this->mock->humainReadable(false))
                ->isEqualTo('yesterday')
            ->string($this->mock->humainReadable(true))
                ->isEqualTo('yesterday at '.$yesterdayFormat);
        
        $this->assert('test humainReadable : Before yesterday; Same year')
            ->given($this->mock->modify('-1 month'))
            ->given($dateFormat = $this->mock->format($hrFormat['dateSameYear']))
            ->given($timeFormat = $this->mock->format($hrFormat['time']))
            ->string($this->mock->humainReadable())
                ->isEqualTo('the '.$dateFormat.' at '.$timeFormat)
            ->string($this->mock->humainReadable(false))
                ->isEqualTo('the '.$dateFormat)
            ->string($this->mock->humainReadable(true))
                ->isEqualTo('the '.$dateFormat.' at '.$timeFormat);
        
        $this->assert('test humainReadable : Before yesterday; Different year')
            ->given($this->mock->modify('-1 year'))
            ->given($dateFormat = $this->mock->format($hrFormat['dateDifferentYear']))
            ->given($timeFormat = $this->mock->format($hrFormat['time']))
            ->string($this->mock->humainReadable())
                ->isEqualTo('the '.$dateFormat.' at '.$timeFormat)
            ->string($this->mock->humainReadable(false))
                ->isEqualTo('the '.$dateFormat)
            ->string($this->mock->humainReadable(true))
                ->isEqualTo('the '.$dateFormat.' at '.$timeFormat);
    }
}

/**
 * Mock de la class à tester
 */
class MockDates extends \BFW\Dates
{
    /**
     * Accesseur get
     */
    public function __get($name)
    {
        return $this->$name;
    }
}


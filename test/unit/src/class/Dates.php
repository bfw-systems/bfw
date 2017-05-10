<?php

namespace BFW\test\unit;

use \atoum;
use \BFW\test\unit\mocks\Dates as MockDates;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Dates extends atoum
{
    /**
     * @var $mock Mock instance
     */
    protected $mock;

    /**
     * Instantiate the mock
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        $this->mock = new MockDates;
    }
    
    /**
     * Test method for getHumanReadableI18n()
     * 
     * @return void
     */
    public function testGetHumanReadableI18n()
    {
        $this->assert('test getHumanReadableI18n')
            ->array(MockDates::getHumanReadableI18n())
                ->isEqualTo([
                    'now'       => 'now',
                    'since'     => 'since',
                    'in'        => 'in',
                    'yesterday' => 'yesterday',
                    'the'       => 'the',
                    'at'        => 'at'
                ]);
    }
    
    /**
     * Test method for setHumanReadableI18nKey()
     * 
     * @return void
     */
    public function testSetHumanReadableI18nKey()
    {
        $key   = 'now';
        $value = 'maintenant';
        
        $this->assert('test setHumanReadableI18nKey')
            ->given(MockDates::setHumanReadableI18nKey($key, $value))
            ->array(MockDates::getHumanReadableI18n())
                ->isEqualTo([
                    'now'       => 'maintenant',
                    'since'     => 'since',
                    'in'        => 'in',
                    'yesterday' => 'yesterday',
                    'the'       => 'the',
                    'at'        => 'at'
                ]);
        
        $key   = 'nox';
        $value = 'maintenant';
        
        $this->assert('test setHumanReadableI18nKey new key')
            ->given(MockDates::setHumanReadableI18nKey($key, $value))
            ->array(MockDates::getHumanReadableI18n())
                ->isEqualTo([
                    'now'       => 'maintenant',
                    'since'     => 'since',
                    'in'        => 'in',
                    'yesterday' => 'yesterday',
                    'the'       => 'the',
                    'at'        => 'at',
                    'nox'       => 'maintenant'
                ]);
    }
    
    /**
     * Test method for setHumanReadableI18n()
     * 
     * @return void
     */
    public function testSetHumanReadableI18n()
    {
        $newValue = [
            'test' => 'test',
            'test2' => 'test2'
        ];
        
        $this->assert('test setHumanReadableI18n')
            ->given(MockDates::setHumanReadableI18n($newValue))
            ->array(MockDates::getHumanReadableI18n())
                ->isEqualTo($newValue);
    }
    
    /**
     * Test method for getHumanReadableFormats()
     * 
     * @return void
     */
    public function testGetHumanReadableFormats()
    {
        $this->assert('test getHumanReadableFormats')
            ->array(MockDates::getHumanReadableFormats())
                ->isEqualTo([
                    'dateSameYear'      => 'm-d',
                    'dateDifferentYear' => 'Y-m-d',
                    'time'              => 'H:i'
                ]);
    }
    
    /**
     * Test method for setHumanReadableFormatsKey()
     * 
     * @return void
     */
    public function testSetHumanReadableFormatsKey()
    {
        $key   = 'time';
        $value = 'H:i:s';
        
        $this->assert('test setHumanReadableFormatsKey')
            ->given(MockDates::setHumanReadableFormatsKey($key, $value))
            ->array(MockDates::getHumanReadableFormats())
                ->isEqualTo([
                    'dateSameYear'      => 'm-d',
                    'dateDifferentYear' => 'Y-m-d',
                    'time'              => 'H:i:s'
                ]);
        
        $key   = 'gmt';
        $value = 'O';
        
        $this->assert('test setHumanReadableFormatsKey new key')
            ->given(MockDates::setHumanReadableFormatsKey($key, $value))
            ->array(MockDates::getHumanReadableFormats())
                ->isEqualTo([
                    'dateSameYear'      => 'm-d',
                    'dateDifferentYear' => 'Y-m-d',
                    'time'              => 'H:i:s',
                    'gmt'               => 'O'
                ]);
    }
    
    /**
     * Test method for setHumanReadableFormats()
     * 
     * @return void
     */
    public function testSetHumanReadableFormats()
    {
        $newValue = [
            'test'  => 'test',
            'test2' => 'test2'
        ];
        
        $this->assert('test setHumanReadableFormats')
            ->given(MockDates::setHumanReadableFormats($newValue))
            ->array(MockDates::getHumanReadableFormats())
                ->isEqualTo($newValue);
    }
    
    /**
     * Test method for getModifyNewKeywords()
     * 
     * @return void
     */
    public function testGetModifyNewKeywords()
    {
        $this->assert('test getModifyNewKeywords')
            ->array(MockDates::getModifyNewKeywords())
                ->isEqualTo([
                    'an'       => 'year',
                    'ans'      => 'year',
                    'mois'     => 'month',
                    'jour'     => 'day',
                    'jours'    => 'day',
                    'heure'    => 'hour',
                    'heures'   => 'hour',
                    'minutes'  => 'minute',
                    'seconde'  => 'second',
                    'secondes' => 'second'
                ]);
    }
    
    /**
     * Test method for setModifyNewKeywords()
     * 
     * @return void
     */
    public function testSetModifyNewKeywords()
    {
        $newValue = [
            'test'  => 'test',
            'test2' => 'test2'
        ];
        
        $this->assert('test setModifyNewKeywords')
            ->given(MockDates::setModifyNewKeywords($newValue))
            ->array(MockDates::getModifyNewKeywords())
                ->isEqualTo($newValue);
    }
    
    /**
     * Test method for getDate()
     * 
     * @return void
     */
    public function testGetDate()
    {
        $this->assert('test getDate')
            ->string($this->mock->getDate())
                ->isEqualTo($this->mock->format('Y-m-d H:i:sO'));
    }
    
    /**
     * Test method for getYear()
     * 
     * @return void
     */
    public function testGetYear()
    {
        $this->assert('test getYear')
            ->integer($this->mock->getYear())
                ->isEqualTo((int) $this->mock->format('Y'));
    }
    
    /**
     * Test method for getMonth()
     * 
     * @return void
     */
    public function testGetMonth()
    {
        $this->assert('test getMonth')
            ->integer($this->mock->getMonth())
                ->isEqualTo((int) $this->mock->format('m'));
    }
    
    /**
     * Test method for getDay()
     * 
     * @return void
     */
    public function testGetDay()
    {
        $this->assert('test getDay')
            ->integer($this->mock->getDay())
                ->isEqualTo((int) $this->mock->format('d'));
    }
    
    /**
     * Test method for getHour()
     * 
     * @return void
     */
    public function testGetHour()
    {
        $this->assert('test getHour')
            ->integer($this->mock->getHour())
                ->isEqualTo((int) $this->mock->format('H'));
    }
    
    /**
     * Test method for getMinute()
     * 
     * @return void
     */
    public function testGetMinute()
    {
        $this->assert('test getMinute')
            ->integer($this->mock->getMinute())
                ->isEqualTo((int) $this->mock->format('i'));
    }
    
    /**
     * Test method for getSecond()
     * 
     * @return void
     */
    public function testGetSecond()
    {
        $this->assert('test getSecond')
            ->integer($this->mock->getSecond())
                ->isEqualTo((int) $this->mock->format('s'));
    }
    
    /**
     * Test method for getZone()
     * 
     * @return void
     */
    public function testGetZone()
    {
        $this->assert('test getZone')
            ->string($this->mock->getZone())
                ->isEqualTo($this->mock->format('P'));
    }
    
    /**
     * Test method for modify()
     * 
     * @return void
     */
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
    
    /**
     * Test method for getSqlFormat()
     * 
     * @return void
     */
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
    
    /**
     * Test method for lstTimeZone()
     * 
     * @return void
     */
    public function testLstTimeZone()
    {
        $dateTimeZone = new \DateTimeZone('Europe/Paris');
        $lstTimeZone  = $dateTimeZone->listIdentifiers();
        
        $this->assert('test lstTimeZone')
            ->array($this->mock->lstTimeZone())
                ->isEqualTo($lstTimeZone);
    }
    
    /**
     * Test method for lstTimeZoneContinent()
     * 
     * @return void
     */
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
    
    /**
     * Test method for lstTimeZonePays()
     * 
     * @return void
     */
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
    
    /**
     * Test method for humanReadable()
     * 
     * @return void
     */
    public function testHumanReadable()
    {
        $hrFormat = MockDates::getHumanReadableFormats();
        
        /*
         * Disable because this test fail all, a "lag" return "since 1s"
        $this->assert('test humaneReadable : now')
            ->string($this->mock->humanReadable())
                ->isEqualTo($hrI18n['now']);
        */
        
        $this->assert('test humanReadable : Since')
            /*->given($this->mock->modify('-10 second'))
            ->string($this->mock->humanReadable())
                ->isEqualTo('Since 10s')
            ->string($this->mock->humanReadable(false, true))
                ->isEqualTo('since 10s')*/
            ->given($this->mock->modify('-30 minute'))
            ->string($this->mock->humanReadable())
                ->isEqualTo('since 30min')
            ->string($this->mock->humanReadable(false))
                ->isEqualTo('since 30min')
            ->given($this->mock->modify('-1 hour'))
            ->string($this->mock->humanReadable())
                ->isEqualTo('since 1h')
            ->string($this->mock->humanReadable(false))
                ->isEqualTo('since 1h')
            ->given($this->mock->modify('+3 hour'))
            ->string($this->mock->humanReadable())
                ->isEqualTo('in 1h')
            ->string($this->mock->humanReadable(false))
                ->isEqualTo('in 1h');
        
        $this->assert('test humanReadable : Yesterday')
            ->given($this->mock->modify('-28 hour'))
            ->given($yesterdayFormat = $this->mock->format($hrFormat['time']))
            ->string($this->mock->humanReadable())
                ->isEqualTo('yesterday at '.$yesterdayFormat)
            ->string($this->mock->humanReadable(false))
                ->isEqualTo('yesterday')
            ->string($this->mock->humanReadable(true))
                ->isEqualTo('yesterday at '.$yesterdayFormat);
        
        //Not testable current january month because the month before is not
        //in the same year.
        if ((int) $this->mock->format('m') !== 1) {
            $this->assert('test humanReadable : Before yesterday; Same year')
                ->given($this->mock->modify('-1 month'))
                ->given($dateFormat = $this->mock->format($hrFormat['dateSameYear']))
                ->given($timeFormat = $this->mock->format($hrFormat['time']))
                ->string($this->mock->humanReadable())
                    ->isEqualTo('the '.$dateFormat.' at '.$timeFormat)
                ->string($this->mock->humanReadable(false))
                    ->isEqualTo('the '.$dateFormat)
                ->string($this->mock->humanReadable(true))
                    ->isEqualTo('the '.$dateFormat.' at '.$timeFormat);
        }
        
        $this->assert('test humanReadable : Before yesterday; Different year')
            ->given($this->mock->modify('-1 year'))
            ->given($dateFormat = $this->mock->format($hrFormat['dateDifferentYear']))
            ->given($timeFormat = $this->mock->format($hrFormat['time']))
            ->string($this->mock->humanReadable())
                ->isEqualTo('the '.$dateFormat.' at '.$timeFormat)
            ->string($this->mock->humanReadable(false))
                ->isEqualTo('the '.$dateFormat)
            ->string($this->mock->humanReadable(true))
                ->isEqualTo('the '.$dateFormat.' at '.$timeFormat);
    }
}

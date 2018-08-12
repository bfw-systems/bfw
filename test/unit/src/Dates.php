<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Dates extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../..');
        $this->createApp();
        $this->initApp();
        
        $this->mockGenerator
            ->makeVisible('obtainNewKeywordsForModify')
            ->makeVisible('modifyWithOthersKeywords')
            ->makeVisible('humanDateNow')
            ->makeVisible('humanDateToday')
            ->makeVisible('humanDateYesterday')
            ->makeVisible('humanDateTomorrow')
            ->makeVisible('humanDateOther')
            ->generate('BFW\Dates')
        ;
        
        $methodsWithFixedDate = [
            'testGetDate',
            'testGetYear',
            'testGetMonth',
            'testGetDay',
            'testGetHour',
            'testGetMinute',
            'testGetSecond',
            'testGetZone',
            'testGetSqlFormat'
        ];
        
        if (in_array($testMethod, $methodsWithFixedDate)) {
            $this->mock = new \mock\BFW\Dates('2018-02-01 13:10:23+0200');
        } else {
            $this->mock = new \mock\BFW\Dates;
        }
    }
    
    public function testGetAndSetHumanReadableI18n()
    {
        $this->assert('test Dates::getHumanReadableI18n for default Values')
            ->array(\BFW\Dates::getHumanReadableI18n())
                ->isEqualTo([
                    'now'       => 'now',
                    'since'     => 'since',
                    'in'        => 'in',
                    'yesterday' => 'yesterday',
                    'tomorrow'  => 'tomorrow',
                    'the'       => 'the',
                    'at'        => 'at'
                ])
        ;
        
        $this->assert('test Dates::setHumanReadableI18n')
            ->variable(\BFW\Dates::setHumanReadableI18n([
                'now'       => 'maintenant',
                'since'     => 'depuis',
                'in'        => 'dans',
                'yesterday' => 'hier',
                'tomorrow'  => 'demain',
                'the'       => 'le',
                'at'        => 'à'
            ]))
                ->isNull()
            ->array(\BFW\Dates::getHumanReadableI18n())
                ->isEqualTo([
                    'now'       => 'maintenant',
                    'since'     => 'depuis',
                    'in'        => 'dans',
                    'yesterday' => 'hier',
                    'tomorrow'  => 'demain',
                    'the'       => 'le',
                    'at'        => 'à'
                ])
        ;
    }
    
    public function testSetHumanReadableI18nKey()
    {
        $this->assert('test Dates::setHumanReadableI18nKey')
            ->variable(\BFW\Dates::setHumanReadableI18nKey('since', 'depuis'))
                ->isNull()
            ->array(\BFW\Dates::getHumanReadableI18n())
                ->isEqualTo([
                    'now'       => 'now',
                    'since'     => 'depuis',
                    'in'        => 'in',
                    'yesterday' => 'yesterday',
                    'tomorrow'  => 'tomorrow',
                    'the'       => 'the',
                    'at'        => 'at'
                ])
        ;
    }
    
    public function testGetAndSetHumanReadableFormats()
    {
        $this->assert('test Dates::getHumanReadableFormats for default Values')
            ->array(\BFW\Dates::getHumanReadableFormats())
                ->isEqualTo([
                    'dateSameYear'      => 'm-d',
                    'dateDifferentYear' => 'Y-m-d',
                    'time'              => 'H:i'
                ])
        ;
        
        $this->assert('test Dates::setHumanReadableFormats')
            ->variable(\BFW\Dates::setHumanReadableFormats([
                'dateSameYear'      => 'd/m',
                'dateDifferentYear' => 'd/m/Y',
                'time'              => 'H:i'
            ]))
                ->isNull()
            ->array(\BFW\Dates::getHumanReadableFormats())
                ->isEqualTo([
                    'dateSameYear'      => 'd/m',
                    'dateDifferentYear' => 'd/m/Y',
                    'time'              => 'H:i'
                ])
        ;
    }
    
    public function testSetHumanReadableFormatsKey()
    {
        $this->assert('test Dates::setHumanReadableFormatsKey')
            ->variable(\BFW\Dates::setHumanReadableFormatsKey('dateDifferentYear', 'd/m/Y'))
                ->isNull()
            ->array(\BFW\Dates::getHumanReadableFormats())
                ->isEqualTo([
                    'dateSameYear'      => 'm-d',
                    'dateDifferentYear' => 'd/m/Y',
                    'time'              => 'H:i'
                ])
        ;
    }
    
    public function testGetAndSetModifyNewKeywords()
    {
        $this->assert('test Dates::getModifyNewKeywords for default Values')
            ->array(\BFW\Dates::getModifyNewKeywords())
                ->isEmpty()
        ;
        
        $this->assert('test Dates::setModifyNewKeywords')
            ->variable(\BFW\Dates::setModifyNewKeywords([
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

            ]))
                ->isNull()
            ->array(\BFW\Dates::getModifyNewKeywords())
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
                ])
        ;
    }
    
    public function testGetDate()
    {
        $this->assert('test Dates::getDate')
            ->string($this->mock->getDate())
                ->isEqualTo('2018-02-01 13:10:23+0200')
        ;
    }
    
    public function testGetYear()
    {
        $this->assert('test Dates::getYear')
            ->integer($this->mock->getYear())
                ->isEqualTo(2018)
        ;
    }
    
    public function testGetMonth()
    {
        $this->assert('test Dates::getMonth')
            ->integer($this->mock->getMonth())
                ->isEqualTo(02)
        ;
    }
    
    public function testGetDay()
    {
        $this->assert('test Dates::getDay')
            ->integer($this->mock->getDay())
                ->isEqualTo(01)
        ;
    }
    
    public function testGetHour()
    {
        $this->assert('test Dates::getHour')
            ->integer($this->mock->getHour())
                ->isEqualTo(13)
        ;
    }
    
    public function testGetMinute()
    {
        $this->assert('test Dates::getMinute')
            ->integer($this->mock->getMinute())
                ->isEqualTo(10)
        ;
    }
    
    public function testGetSecond()
    {
        $this->assert('test Dates::getSecond')
            ->integer($this->mock->getSecond())
                ->isEqualTo(23)
        ;
    }
    
    public function testGetZone()
    {
        $this->assert('test Dates::getZone')
            ->string($this->mock->getZone())
                ->isEqualTo('+02:00')
        ;
    }
    
    public function testModify()
    {
        $this->assert('test Dates::modify with native keyword')
            ->given($keywordUsed = '')
            ->if($this->mock->setTime(16, 36, 23))
            ->and($this->calling($this->mock)->modifyWithOthersKeywords = function($modify) use (&$keywordUsed) {
                $keywordUsed = $modify;
            })
            ->then
            ->object($this->mock->modify('+1 hour'))
                ->isIdenticalTo($this->mock)
            ->then
            ->string($keywordUsed)
                ->isEmpty() //Function not called
            ->integer($this->mock->getHour())
                ->isEqualTo(17)
        ;
        
        $this->assert('test Dates::modify with personal keyword')
            ->given($keywordUsed = '')
            ->if($this->mock->setTime(16, 36, 23))
            ->and($this->calling($this->mock)->modifyWithOthersKeywords = function($modify) use (&$keywordUsed) {
                $keywordUsed = $modify;
            })
            ->then
            ->object($this->mock->modify('+1 heure'))
                ->isIdenticalTo($this->mock)
            ->then
            ->string($keywordUsed)
                ->isEqualTo('+1 heure')
            ->integer($this->mock->getHour())
                ->isEqualTo(16) //No change because mocking
        ;
    }
    
    public function testObtainNewKeywordsForModify()
    {
        $this->assert('test Dates::obtainNewKeywordsForModify without keyword')
            ->array($obj = $this->invoke($this->mock)->obtainNewKeywordsForModify())
                ->hasKeys(['search', 'replace'])
            ->array($obj['search'])
                ->isEmpty()
            ->array($obj['replace'])
                ->isEmpty()
        ;
        
        $this->assert('test Dates::obtainNewKeywordsForModify with keywords')
            ->if(\BFW\Dates::setModifyNewKeywords([
                'an'      => 'year',
                'mois'    => 'month',
                'jour'    => 'day',
                'heure'   => 'hour',
                'seconde' => 'second'
            ]))
            ->then
            
            ->array($obj = $this->invoke($this->mock)->obtainNewKeywordsForModify())
                ->hasKeys(['search', 'replace'])
            ->array($obj['search'])
                ->isEqualTo(['an', 'mois', 'jour', 'heure', 'seconde'])
            ->array($obj['replace'])
                ->isEqualTo(['year', 'month', 'day', 'hour', 'second'])
        ;
    }
    
    public function testModifyWithOthersKeywords()
    {
        $this->assert('test Dates::modifyWithOthersKeywords with bad pattern')
            ->given($this->calling($this->mock)->obtainNewKeywordsForModify = [
                'search'  => [],
                'replace' => []
            ])
            ->exception(function() {
                $this->invoke($this->mock)->modifyWithOthersKeywords('test');
            })
                ->hasCode(\BFW\Dates::ERR_MODIFY_PATTERN_NOT_MATCH)
        ;
            
        $this->assert('test Dates::modifyWithOthersKeywords with unknown keyword')
            ->given($this->calling($this->mock)->obtainNewKeywordsForModify = [
                'search'  => ['heure'],
                'replace' => ['hour']
            ])
            ->exception(function() {
                $this->invoke($this->mock)->modifyWithOthersKeywords('+1 jour');
            })
                ->hasCode(\BFW\Dates::ERR_MODIFY_UNKNOWN_MODIFIER)
        ;
            
        $this->assert('test Dates::modifyWithOthersKeywords with correct keyword')
            ->given($this->calling($this->mock)->obtainNewKeywordsForModify = [
                'search'  => ['heure'],
                'replace' => ['hour']
            ])
            ->if($this->mock->setTime(16, 36, 23))
            ->then
            ->variable($this->invoke($this->mock)->modifyWithOthersKeywords('+1 heure'))
                ->isNull()
            ->integer($this->mock->getHour())
                ->isEqualTo(17)
        ;
    }
    
    public function testGetSqlFormat()
    {
        $this->assert('test Dates::getSqlFormat for string format without zone')
            ->string($this->mock->getSqlFormat(false))
                ->isEqualTo('2018-02-01 13:10:23')
        ;
        
        $this->assert('test Dates::getSqlFormat for array format without zone')
            ->array($this->mock->getSqlFormat(true))
                ->isEqualTo(['2018-02-01', '13:10:23'])
        ;
        
        $this->assert('test Dates::getSqlFormat for string format with zone')
            ->string($this->mock->getSqlFormat(false, true))
                ->isEqualTo('2018-02-01 13:10:23+0200')
        ;
        
        $this->assert('test Dates::getSqlFormat for array format with zone')
            ->array($this->mock->getSqlFormat(true, true))
                ->isEqualTo(['2018-02-01', '13:10:23+0200'])
        ;
    }
    
    public function testLstTimeZone()
    {
        $this->assert('test Dates::lstTimeZone')
            ->given($dateTimeZone = new \DateTimeZone('Europe/Paris'))
            ->and($lstTimeZone  = $dateTimeZone->listIdentifiers())
            ->then
            
            ->array($this->mock->lstTimeZone())
                ->isEqualTo($lstTimeZone)
        ;
    }
    
    public function testLstTimeZoneContinent()
    {
        $this->assert('test Dates::lstTimeZoneContinent')
            ->array($this->mock->lstTimeZoneContinent())
                ->isEqualTo([
                    'Africa',
                    'America',
                    'Antartica',
                    'Arctic',
                    'Asia',
                    'Atlantic',
                    'Australia',
                    'Europe',
                    'Indian',
                    'Pacific'
                ])
        ;
    }
    
    public function testLstTimeZoneCountries()
    {
        $this->assert('test Dates::lstTimeZoneCountries')
            ->array($lstCountries = $this->mock->lstTimeZoneCountries('Antarctica'))
                ->isNotEmpty()
                ->contains('Antarctica/McMurdo')
        ;
    }
    
    protected function prepareHumanReadable()
    {
        $this
            ->and($this->calling($this->mock)->humanDateNow = function($parsedTxt) {
                $parsedTxt->date = 'dateNow';
                $parsedTxt->time = 'timeNow';
            })
            ->and($this->calling($this->mock)->humanDateYesterday = function($parsedTxt) {
                $parsedTxt->date = 'dateYesterday';
                $parsedTxt->time = 'timeYesterday';
            })
            ->and($this->calling($this->mock)->humanDateTomorrow = function($parsedTxt) {
                $parsedTxt->date = 'dateTomorrow';
                $parsedTxt->time = 'timeTomorrow';
            })
            ->and($this->calling($this->mock)->humanDateToday = function($parsedTxt, $diff) {
                $parsedTxt->date = 'dateToday';
                $parsedTxt->time = 'timeToday';
            })
            ->and($this->calling($this->mock)->humanDateOther = function($parsedTxt, $current) {
                $parsedTxt->date = 'dateOther';
                $parsedTxt->time = 'timeOther';
            })
        ;
    }
    
    public function testHumanReadable()
    {
        $this->assert('test Dates::humanReadable call humanDateNow')
            //Disabled because have exactly the same second is too hard
            //Not always working :/
        ;
        
        $this->assert('test Dates::humanReadable call humanDateYesterday')
            ->if($this->mock = new \mock\BFW\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('-25 hours'))
            ->then
            
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateYesterday timeYesterday')
            ->mock($this->mock)->call('humanDateNow')->never()
            ->mock($this->mock)->call('humanDateYesterday')->once()
            ->mock($this->mock)->call('humanDateTomorrow')->never()
            ->mock($this->mock)->call('humanDateToday')->never()
            ->mock($this->mock)->call('humanDateOther')->never()
            
            ->then
            //-1 month -25 hours => Not yesterday
            ->and($this->mock->modify('-1 month'))
            ->then
            ->variable($this->mock->humanReadable())
            ->mock($this->mock)->call('humanDateYesterday')->once()
            
            ->then
            //-1 year -25 hours => Not yesterday
            ->and($this->mock->modify('-11 month'))
            ->then
            ->variable($this->mock->humanReadable())
            ->mock($this->mock)->call('humanDateYesterday')->once()
        ;
        
        $this->assert('test Dates::humanReadable call humanDateTomorrow - tomorrow')
            ->if($this->mock = new \mock\BFW\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('+25 hours'))
            ->then
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateTomorrow timeTomorrow')
            ->mock($this->mock)->call('humanDateNow')->never()
            ->mock($this->mock)->call('humanDateYesterday')->never()
            ->mock($this->mock)->call('humanDateTomorrow')->once()
            ->mock($this->mock)->call('humanDateToday')->never()
            ->mock($this->mock)->call('humanDateOther')->never()
            
            ->then
            //+1 month +25 hours => Not tomorrow
            ->and($this->mock->modify('+1 month'))
            ->then
            ->variable($this->mock->humanReadable())
            ->mock($this->mock)->call('humanDateTomorrow')->once()
            
            ->then
            //+1 year +25 hours => Not tomorrow
            ->and($this->mock->modify('+11 month'))
            ->then
            ->variable($this->mock->humanReadable())
            ->mock($this->mock)->call('humanDateTomorrow')->once()
        ;
        
        $this->assert('test Dates::humanReadable call humanDateToday')
            ->if($this->mock = new \mock\BFW\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('-20 hours'))
            ->then
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateToday timeToday')
            ->mock($this->mock)->call('humanDateNow')->never()
            ->mock($this->mock)->call('humanDateYesterday')->never()
            ->mock($this->mock)->call('humanDateTomorrow')->never()
            ->mock($this->mock)->call('humanDateToday')->once()
            ->mock($this->mock)->call('humanDateOther')->never()
        ;
        
        $this->assert('test Dates::humanReadable call humanDateOther - 2 days before')
            ->if($this->mock = new \mock\BFW\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('-50 hours'))
            ->then
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateOther timeOther')
            ->mock($this->mock)->call('humanDateNow')->never()
            ->mock($this->mock)->call('humanDateYesterday')->never()
            ->mock($this->mock)->call('humanDateTomorrow')->never()
            ->mock($this->mock)->call('humanDateToday')->never()
            ->mock($this->mock)->call('humanDateOther')->once()
        ;
        
        $this->assert('test Dates::humanReadable call humanDateOther - 2 days after')
            ->if($this->mock = new \mock\BFW\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('+50 hours'))
            ->then
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateOther timeOther')
            ->mock($this->mock)->call('humanDateNow')->never()
            ->mock($this->mock)->call('humanDateYesterday')->never()
            ->mock($this->mock)->call('humanDateTomorrow')->never()
            ->mock($this->mock)->call('humanDateToday')->never()
            ->mock($this->mock)->call('humanDateOther')->once()
        ;
        
        $this->assert('test Dates::humanReadable call humanDateOther - last month')
            ->if($this->mock = new \mock\BFW\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('-1 month'))
            ->then
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateOther timeOther')
            ->mock($this->mock)->call('humanDateNow')->never()
            ->mock($this->mock)->call('humanDateYesterday')->never()
            ->mock($this->mock)->call('humanDateTomorrow')->never()
            ->mock($this->mock)->call('humanDateToday')->never()
            ->mock($this->mock)->call('humanDateOther')->once()
        ;
        
        $this->assert('test Dates::humanReadable call humanDateOther - last year')
            ->if($this->mock = new \mock\BFW\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('-1 year'))
            ->then
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateOther timeOther')
            ->mock($this->mock)->call('humanDateNow')->never()
            ->mock($this->mock)->call('humanDateYesterday')->never()
            ->mock($this->mock)->call('humanDateTomorrow')->never()
            ->mock($this->mock)->call('humanDateToday')->never()
            ->mock($this->mock)->call('humanDateOther')->once()
        ;
    }
    
    public function testHumanReadableWithoutTime()
    {
        $this->assert('test Dates::humanReadable without time part')
            ->if($this->mock = new \mock\BFW\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('-25 hours'))
            ->then
            
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateYesterday timeYesterday')
            ->string($this->mock->humanReadable(true))
                ->isEqualTo('dateYesterday timeYesterday')
            ->string($this->mock->humanReadable(false))
                ->isEqualTo('dateYesterday')
        ;
    }
    
    /**
     * Issue #81
     */
    public function testHumanReadableDifferentTimeZone()
    {
        $this->assert('test Dates::humanReadable with different timezone')
            ->if(ini_set('date.timezone', 'Europe/Paris'))
            ->and($this->mock = new \mock\BFW\Dates)
            ->and($this->prepareHumanReadable())
            ->given($dateTimeZone = new \DateTimeZone('America/New_York')) //-6/7 hours
            ->and($this->mock->setTimezone($dateTimeZone))
            ->and($this->mock->modify('-20 hours'))
            ->then
            
            //\DateTime::diff use same TimeZone to compare. So it's the same
            //day and not yesterday :)
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateToday timeToday')
        ;
    }
    
    public function testHumanDateNow()
    {
        $this->assert('test Dates::humanDateNow')
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->then
            ->variable($this->invoke($this->mock)->humanDateNow($parsedTxt))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo(\BFW\Dates::getHumanReadableI18n()['now'])
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
    }
    
    public function testHumanDateToday()
    {
        $this->assert('test Dates::humanDateToday - 5 seconds before')
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->given($now = new \DateTime)
            ->given($toDiff = new \DateTime)
            ->then
            
            ->if($toDiff->modify('-5 seconds'))
            ->and($diff = $toDiff->diff($now))
            ->then
            
            ->variable($this->invoke($this->mock)->humanDateToday($parsedTxt, $diff))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo(\BFW\Dates::getHumanReadableI18n()['since'].' 5s')
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
        
        $this->assert('test Dates::humanDateToday - 10 minutes before')
            ->if($toDiff->modify('-10 minutes'))
            ->and($diff = $toDiff->diff($now))
            ->then
            
            ->variable($this->invoke($this->mock)->humanDateToday($parsedTxt, $diff))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo(\BFW\Dates::getHumanReadableI18n()['since'].' 10min')
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
        
        $this->assert('test Dates::humanDateToday - 2 hours before')
            ->if($toDiff->modify('-2 hours'))
            ->and($diff = $toDiff->diff($now))
            ->then
            
            ->variable($this->invoke($this->mock)->humanDateToday($parsedTxt, $diff))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo(\BFW\Dates::getHumanReadableI18n()['since'].' 2h')
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
        
        $this->assert('test Dates::humanDateToday - 5 seconds after')
            ->given($now = new \DateTime)
            ->given($toDiff = new \DateTime)
            ->then
            
            ->if($toDiff->modify('+5 seconds'))
            ->and($diff = $toDiff->diff($now))
            ->then
            
            ->variable($this->invoke($this->mock)->humanDateToday($parsedTxt, $diff))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo(\BFW\Dates::getHumanReadableI18n()['in'].' 5s')
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
        
        $this->assert('test Dates::humanDateToday - 10 minutes after')
            ->if($toDiff->modify('+10 minutes'))
            ->and($diff = $toDiff->diff($now))
            ->then
            
            ->variable($this->invoke($this->mock)->humanDateToday($parsedTxt, $diff))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo(\BFW\Dates::getHumanReadableI18n()['in'].' 10min')
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
        
        $this->assert('test Dates::humanDateToday - 2 hours after')
            ->if($toDiff->modify('+2 hours'))
            ->and($diff = $toDiff->diff($now))
            ->then
            
            ->variable($this->invoke($this->mock)->humanDateToday($parsedTxt, $diff))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo(\BFW\Dates::getHumanReadableI18n()['in'].' 2h')
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
    }
    
    public function testHumanDateYesterday()
    {
        $this->assert('test Dates::humanDateYesterday')
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->given($i18n = \BFW\Dates::getHumanReadableI18n())
            ->then
            ->variable($this->invoke($this->mock)->humanDateYesterday($parsedTxt))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo($i18n['yesterday'])
            ->string($parsedTxt->time)
                ->isEqualTo($i18n['at'].' '.$this->mock->format('H:i'))
        ;
    }
    
    public function testHumanDateTomorrow()
    {
        $this->assert('test Dates::humanDateTomorrow')
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->given($i18n = \BFW\Dates::getHumanReadableI18n())
            ->then
            ->variable($this->invoke($this->mock)->humanDateTomorrow($parsedTxt))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo($i18n['tomorrow'])
            ->string($parsedTxt->time)
                ->isEqualTo($i18n['at'].' '.$this->mock->format('H:i'))
        ;
    }
    
    public function testHumanDateOther()
    {
        $this->assert('test Dates::humanDateOther - same year')
            ->given($current = new \mock\BFW\Dates)
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->given($i18n = \BFW\Dates::getHumanReadableI18n())
            ->then
            ->variable($this->invoke($this->mock)->humanDateOther($parsedTxt, $current))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo($i18n['the'].' '.$this->mock->format('m-d'))
            ->string($parsedTxt->time)
                ->isEqualTo($i18n['at'].' '.$this->mock->format('H:i'))
        ;
        
        $this->assert('test Dates::humanDateOther - different year')
            ->given($current = new \mock\BFW\Dates)
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->given($i18n = \BFW\Dates::getHumanReadableI18n())
            ->if($this->mock->modify('-1 year'))
            ->then
            ->variable($this->invoke($this->mock)->humanDateOther($parsedTxt, $current))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo($i18n['the'].' '.$this->mock->format('Y-m-d'))
            ->string($parsedTxt->time)
                ->isEqualTo($i18n['at'].' '.$this->mock->format('H:i'))
        ;
    }
}
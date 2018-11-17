<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Dates extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../..');
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
            ->makeVisible('humanDateIsYesterdayOrTomorrow')
            ->makeVisible('humanParseDateAndTimeText')
            ->generate('BFW\Helpers\Dates')
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
            $this->mock = new \mock\BFW\Helpers\Dates('2018-02-01 13:10:23+0200');
        } else {
            $this->mock = new \mock\BFW\Helpers\Dates;
        }
    }
    
    public function testGetAndSetHumanReadableI18n()
    {
        $this->assert('test Helpers\Dates::getHumanReadableI18n for default Values')
            ->array(\BFW\Helpers\Dates::getHumanReadableI18n())
                ->isEqualTo([
                    'now'          => 'now',
                    'today_past'   => '{time} ago',
                    'today_future' => 'in {time}',
                    'yesterday'    => 'yesterday',
                    'tomorrow'     => 'tomorrow',
                    'others'       => 'the {date}',
                    'time_part'    => ' at {time}'
                ])
        ;
        
        $this->assert('test Helpers\Dates::setHumanReadableI18n')
            ->variable(\BFW\Helpers\Dates::setHumanReadableI18n([
                'now'          => 'maintenant',
                'today_past'   => 'il y a {time}',
                'today_future' => 'dans {time}',
                'yesterday'    => 'hier',
                'tomorrow'     => 'demain',
                'others'       => 'le {date}',
                'time_part'    => ' à {time}'
            ]))
                ->isNull()
            ->array(\BFW\Helpers\Dates::getHumanReadableI18n())
                ->isEqualTo([
                    'now'          => 'maintenant',
                    'today_past'   => 'il y a {time}',
                    'today_future' => 'dans {time}',
                    'yesterday'    => 'hier',
                    'tomorrow'     => 'demain',
                    'others'       => 'le {date}',
                    'time_part'    => ' à {time}'
                ])
        ;
    }
    
    public function testSetHumanReadableI18nKey()
    {
        $this->assert('test Helpers\Dates::setHumanReadableI18nKey')
            ->variable(\BFW\Helpers\Dates::setHumanReadableI18nKey('today_past', 'il y a {time}'))
                ->isNull()
            ->array(\BFW\Helpers\Dates::getHumanReadableI18n())
                ->isEqualTo([
                    'now'          => 'now',
                    'today_past'   => 'il y a {time}',
                    'today_future' => 'in {time}',
                    'yesterday'    => 'yesterday',
                    'tomorrow'     => 'tomorrow',
                    'others'       => 'the {date}',
                    'time_part'    => ' at {time}'
                ])
        ;
    }
    
    public function testGetAndSetHumanReadableFormats()
    {
        $this->assert('test Helpers\Dates::getHumanReadableFormats for default Values')
            ->array(\BFW\Helpers\Dates::getHumanReadableFormats())
                ->isEqualTo([
                    'dateSameYear'      => 'm-d',
                    'dateDifferentYear' => 'Y-m-d',
                    'time'              => 'H:i'
                ])
        ;
        
        $this->assert('test Helpers\Dates::setHumanReadableFormats')
            ->variable(\BFW\Helpers\Dates::setHumanReadableFormats([
                'dateSameYear'      => 'd/m',
                'dateDifferentYear' => 'd/m/Y',
                'time'              => 'H:i'
            ]))
                ->isNull()
            ->array(\BFW\Helpers\Dates::getHumanReadableFormats())
                ->isEqualTo([
                    'dateSameYear'      => 'd/m',
                    'dateDifferentYear' => 'd/m/Y',
                    'time'              => 'H:i'
                ])
        ;
    }
    
    public function testSetHumanReadableFormatsKey()
    {
        $this->assert('test Helpers\Dates::setHumanReadableFormatsKey')
            ->variable(\BFW\Helpers\Dates::setHumanReadableFormatsKey('dateDifferentYear', 'd/m/Y'))
                ->isNull()
            ->array(\BFW\Helpers\Dates::getHumanReadableFormats())
                ->isEqualTo([
                    'dateSameYear'      => 'm-d',
                    'dateDifferentYear' => 'd/m/Y',
                    'time'              => 'H:i'
                ])
        ;
    }
    
    public function testGetDate()
    {
        $this->assert('test Helpers\Dates::getDate')
            ->string($this->mock->getDate())
                ->isEqualTo('2018-02-01 13:10:23+0200')
        ;
    }
    
    public function testGetYear()
    {
        $this->assert('test Helpers\Dates::getYear')
            ->integer($this->mock->getYear())
                ->isEqualTo(2018)
        ;
    }
    
    public function testGetMonth()
    {
        $this->assert('test Helpers\Dates::getMonth')
            ->integer($this->mock->getMonth())
                ->isEqualTo(02)
        ;
    }
    
    public function testGetDay()
    {
        $this->assert('test Helpers\Dates::getDay')
            ->integer($this->mock->getDay())
                ->isEqualTo(01)
        ;
    }
    
    public function testGetHour()
    {
        $this->assert('test Helpers\Dates::getHour')
            ->integer($this->mock->getHour())
                ->isEqualTo(13)
        ;
    }
    
    public function testGetMinute()
    {
        $this->assert('test Helpers\Dates::getMinute')
            ->integer($this->mock->getMinute())
                ->isEqualTo(10)
        ;
    }
    
    public function testGetSecond()
    {
        $this->assert('test Helpers\Dates::getSecond')
            ->integer($this->mock->getSecond())
                ->isEqualTo(23)
        ;
    }
    
    public function testGetZone()
    {
        $this->assert('test Helpers\Dates::getZone')
            ->string($this->mock->getZone())
                ->isEqualTo('+02:00')
        ;
    }
    
    public function testGetSqlFormat()
    {
        $this->assert('test Helpers\Dates::getSqlFormat for string format without zone')
            ->string($this->mock->getSqlFormat(false))
                ->isEqualTo('2018-02-01 13:10:23')
        ;
        
        $this->assert('test Helpers\Dates::getSqlFormat for array format without zone')
            ->array($this->mock->getSqlFormat(true))
                ->isEqualTo(['2018-02-01', '13:10:23'])
        ;
        
        $this->assert('test Helpers\Dates::getSqlFormat for string format with zone')
            ->string($this->mock->getSqlFormat(false, true))
                ->isEqualTo('2018-02-01 13:10:23+0200')
        ;
        
        $this->assert('test Helpers\Dates::getSqlFormat for array format with zone')
            ->array($this->mock->getSqlFormat(true, true))
                ->isEqualTo(['2018-02-01', '13:10:23+0200'])
        ;
    }
    
    public function testLstTimeZone()
    {
        $this->assert('test Helpers\Dates::lstTimeZone')
            ->given($dateTimeZone = new \DateTimeZone('Europe/Paris'))
            ->and($lstTimeZone  = $dateTimeZone->listIdentifiers())
            ->then
            
            ->array($this->mock->lstTimeZone())
                ->isEqualTo($lstTimeZone)
        ;
    }
    
    public function testLstTimeZoneContinent()
    {
        $this->assert('test Helpers\Dates::lstTimeZoneContinent')
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
        $this->assert('test Helpers\Dates::lstTimeZoneCountries')
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
        $this->assert('test Helpers\Dates::humanReadable call humanDateNow')
            //Disabled because have exactly the same second is too hard
            //Not always working :/
        ;
        
        $this->assert('test Helpers\Dates::humanReadable call humanDateYesterday')
            ->if($this->mock = new \mock\BFW\Helpers\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('-25 hours'))
            ->then
            
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateYesterdaytimeYesterday')
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
        
        $this->assert('test Helpers\Dates::humanReadable call humanDateTomorrow - tomorrow')
            ->if($this->mock = new \mock\BFW\Helpers\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('+25 hours'))
            ->then
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateTomorrowtimeTomorrow')
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
        
        $this->assert('test Helpers\Dates::humanReadable call humanDateToday')
            ->if($this->mock = new \mock\BFW\Helpers\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('-20 hours'))
            ->then
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateTodaytimeToday')
            ->mock($this->mock)->call('humanDateNow')->never()
            ->mock($this->mock)->call('humanDateYesterday')->never()
            ->mock($this->mock)->call('humanDateTomorrow')->never()
            ->mock($this->mock)->call('humanDateToday')->once()
            ->mock($this->mock)->call('humanDateOther')->never()
        ;
        
        $this->assert('test Helpers\Dates::humanReadable call humanDateOther - 2 days before')
            ->if($this->mock = new \mock\BFW\Helpers\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('-50 hours'))
            ->then
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateOthertimeOther')
            ->mock($this->mock)->call('humanDateNow')->never()
            ->mock($this->mock)->call('humanDateYesterday')->never()
            ->mock($this->mock)->call('humanDateTomorrow')->never()
            ->mock($this->mock)->call('humanDateToday')->never()
            ->mock($this->mock)->call('humanDateOther')->once()
        ;
        
        $this->assert('test Helpers\Dates::humanReadable call humanDateOther - 2 days after')
            ->if($this->mock = new \mock\BFW\Helpers\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('+50 hours'))
            ->then
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateOthertimeOther')
            ->mock($this->mock)->call('humanDateNow')->never()
            ->mock($this->mock)->call('humanDateYesterday')->never()
            ->mock($this->mock)->call('humanDateTomorrow')->never()
            ->mock($this->mock)->call('humanDateToday')->never()
            ->mock($this->mock)->call('humanDateOther')->once()
        ;
        
        $this->assert('test Helpers\Dates::humanReadable call humanDateOther - last month')
            ->if($this->mock = new \mock\BFW\Helpers\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('-1 month'))
            ->then
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateOthertimeOther')
            ->mock($this->mock)->call('humanDateNow')->never()
            ->mock($this->mock)->call('humanDateYesterday')->never()
            ->mock($this->mock)->call('humanDateTomorrow')->never()
            ->mock($this->mock)->call('humanDateToday')->never()
            ->mock($this->mock)->call('humanDateOther')->once()
        ;
        
        $this->assert('test Helpers\Dates::humanReadable call humanDateOther - last year')
            ->if($this->mock = new \mock\BFW\Helpers\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('-1 year'))
            ->then
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateOthertimeOther')
            ->mock($this->mock)->call('humanDateNow')->never()
            ->mock($this->mock)->call('humanDateYesterday')->never()
            ->mock($this->mock)->call('humanDateTomorrow')->never()
            ->mock($this->mock)->call('humanDateToday')->never()
            ->mock($this->mock)->call('humanDateOther')->once()
        ;
    }
    
    public function testHumanReadableWithoutTime()
    {
        $this->assert('test Helpers\Dates::humanReadable without time part')
            ->if($this->mock = new \mock\BFW\Helpers\Dates)
            ->and($this->prepareHumanReadable())
            ->and($this->mock->modify('-25 hours'))
            ->then
            
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateYesterdaytimeYesterday')
            ->string($this->mock->humanReadable(true))
                ->isEqualTo('dateYesterdaytimeYesterday')
            ->string($this->mock->humanReadable(false))
                ->isEqualTo('dateYesterday')
        ;
    }
    
    /**
     * Issue #81
     */
    public function testHumanReadableDifferentTimeZone()
    {
        $this->assert('test Helpers\Dates::humanReadable with different timezone')
            ->if(ini_set('date.timezone', 'Europe/Paris'))
            ->and($this->mock = new \mock\BFW\Helpers\Dates)
            ->and($this->prepareHumanReadable())
            ->given($dateTimeZone = new \DateTimeZone('America/New_York')) //-6/7 hours
            ->and($this->mock->setTimezone($dateTimeZone))
            ->and($this->mock->modify('-20 hours'))
            ->then
            
            //\DateTime::diff use same TimeZone to compare. So it's the same
            //day and not yesterday :)
            ->string($this->mock->humanReadable())
                ->isEqualTo('dateTodaytimeToday')
        ;
    }
    
    public function testHumanDateNow()
    {
        $this->assert('test Helpers\Dates::humanDateNow')
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->then
            ->variable($this->invoke($this->mock)->humanDateNow($parsedTxt))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo(\BFW\Helpers\Dates::getHumanReadableI18n()['now'])
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
    }
    
    public function testHumanDateToday()
    {
        $this->assert('test Helpers\Dates::humanDateToday - 5 seconds before')
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->given($now = new \DateTime)
            ->given($toDiff = clone $now)
            ->then
            
            ->if($toDiff->modify('-5 seconds'))
            ->and($diff = $toDiff->diff($now))
            ->then
            
            ->variable($this->invoke($this->mock)->humanDateToday($parsedTxt, $diff))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo('5s ago')
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
        
        $this->assert('test Helpers\Dates::humanDateToday - 10 minutes before')
            ->if($toDiff->modify('-10 minutes'))
            ->and($diff = $toDiff->diff($now))
            ->then
            
            ->variable($this->invoke($this->mock)->humanDateToday($parsedTxt, $diff))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo('10min ago')
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
        
        $this->assert('test Helpers\Dates::humanDateToday - 2 hours before')
            ->if($toDiff->modify('-2 hours'))
            ->and($diff = $toDiff->diff($now))
            ->then
            
            ->variable($this->invoke($this->mock)->humanDateToday($parsedTxt, $diff))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo('2h ago')
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
        
        $this->assert('test Helpers\Dates::humanDateToday - 5 seconds after')
            ->given($now = new \DateTime)
            ->given($toDiff = clone $now)
            ->then
            
            ->if($toDiff->modify('+5 seconds'))
            ->and($diff = $toDiff->diff($now))
            ->then
            
            ->variable($this->invoke($this->mock)->humanDateToday($parsedTxt, $diff))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo('in 5s')
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
        
        $this->assert('test Helpers\Dates::humanDateToday - 10 minutes after')
            ->if($toDiff->modify('+10 minutes'))
            ->and($diff = $toDiff->diff($now))
            ->then
            
            ->variable($this->invoke($this->mock)->humanDateToday($parsedTxt, $diff))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo('in 10min')
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
        
        $this->assert('test Helpers\Dates::humanDateToday - 2 hours after')
            ->if($toDiff->modify('+2 hours'))
            ->and($diff = $toDiff->diff($now))
            ->then
            
            ->variable($this->invoke($this->mock)->humanDateToday($parsedTxt, $diff))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo('in 2h')
            ->string($parsedTxt->time)
                ->isEmpty()
        ;
    }
    
    public function testHumanDateYesterday()
    {
        $this->assert('test Helpers\Dates::humanDateYesterday')
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->given($i18n = \BFW\Helpers\Dates::getHumanReadableI18n())
            ->then
            ->variable($this->invoke($this->mock)->humanDateYesterday($parsedTxt))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo($i18n['yesterday'])
            ->string($parsedTxt->time)
                ->isEqualTo(' at '.$this->mock->format('H:i'))
        ;
    }
    
    public function testHumanDateTomorrow()
    {
        $this->assert('test Helpers\Dates::humanDateTomorrow')
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->given($i18n = \BFW\Helpers\Dates::getHumanReadableI18n())
            ->then
            ->variable($this->invoke($this->mock)->humanDateTomorrow($parsedTxt))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo($i18n['tomorrow'])
            ->string($parsedTxt->time)
                ->isEqualTo(' at '.$this->mock->format('H:i'))
        ;
    }
    
    public function testHumanDateOther()
    {
        $this->assert('test Helpers\Dates::humanDateOther - same year')
            ->given($current = new \mock\BFW\Helpers\Dates)
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->given($i18n = \BFW\Helpers\Dates::getHumanReadableI18n())
            ->then
            ->variable($this->invoke($this->mock)->humanDateOther($parsedTxt, $current))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo('the '.$this->mock->format('m-d'))
            ->string($parsedTxt->time)
                ->isEqualTo(' at '.$this->mock->format('H:i'))
        ;
        
        $this->assert('test Helpers\Dates::humanDateOther - different year before')
            ->given($current = new \mock\BFW\Helpers\Dates)
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->given($i18n = \BFW\Helpers\Dates::getHumanReadableI18n())
            ->if($this->mock->modify('-1 year'))
            ->then
            ->variable($this->invoke($this->mock)->humanDateOther($parsedTxt, $current))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo('the '.$this->mock->format('Y-m-d'))
            ->string($parsedTxt->time)
                ->isEqualTo(' at '.$this->mock->format('H:i'))
        ;
        
        $this->assert('test Helpers\Dates::humanDateOther - different year after')
            ->given($current = new \mock\BFW\Helpers\Dates)
            ->given($parsedTxt = new class {
                public $date = '';
                public $time = '';
            })
            ->given($i18n = \BFW\Helpers\Dates::getHumanReadableI18n())
            ->if($this->mock->modify('+2 year'))
            ->then
            ->variable($this->invoke($this->mock)->humanDateOther($parsedTxt, $current))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo('the '.$this->mock->format('Y-m-d'))
            ->string($parsedTxt->time)
                ->isEqualTo(' at '.$this->mock->format('H:i'))
        ;
    }
    
    public function testHumanDateIsYesterdayOrTomorrow()
    {
        $this->assert('test Helpers\Dates::humanDateIsYesterdayOrTomorrow - out of range')
            ->given($current = new \DateTime)
            ->and($this->mock->modify('+2 days'))
            ->given($diff = $this->mock->diff($current))
            ->then
            
            ->boolean($this->mock->humanDateIsYesterdayOrTomorrow($diff, $current))
                ->isFalse()
            
            ->given($current = new \DateTime)
            ->and($this->mock->modify('-4 days'))
            ->given($diff = $this->mock->diff($current))
            ->then
            
            ->boolean($this->mock->humanDateIsYesterdayOrTomorrow($diff, $current))
                ->isFalse()
        ;
        
        $this->assert('test Helpers\Dates::humanDateIsYesterdayOrTomorrow - yesterday')
            ->given($this->mock = new \mock\BFW\Helpers\Dates('2018-10-31 10:10:23+0200'))
            ->given($current = new \DateTime('2018-11-01 12:10:23+0200'))
            ->given($diff = $this->mock->diff($current))
            ->then
            
            ->boolean($this->mock->humanDateIsYesterdayOrTomorrow($diff, $current))
                ->isTrue()
        ;
        
        $this->assert('test Helpers\Dates::humanDateIsYesterdayOrTomorrow - tomorrow')
            ->given($this->mock = new \mock\BFW\Helpers\Dates('2018-11-02 14:10:23+0200'))
            ->given($current = new \DateTime('2018-11-01 12:10:23+0200'))
            ->given($diff = $this->mock->diff($current))
            ->then
            
            ->boolean($this->mock->humanDateIsYesterdayOrTomorrow($diff, $current))
                ->isTrue()
        ;
        
        $this->assert('test Helpers\Dates::humanDateIsYesterdayOrTomorrow - in the range but no more yesterday')
            ->given($this->mock = new \mock\BFW\Helpers\Dates('2018-10-30 14:10:23+0200'))
            ->given($current = new \DateTime('2018-11-01 12:10:23+0200'))
            ->given($diff = $this->mock->diff($current))
            ->then
            
            ->boolean($this->mock->humanDateIsYesterdayOrTomorrow($diff, $current))
                ->isFalse()
        ;
        
        $this->assert('test Helpers\Dates::humanDateIsYesterdayOrTomorrow - in the range but no more tomorrow')
            ->given($this->mock = new \mock\BFW\Helpers\Dates('2018-11-03 10:10:23+0200'))
            ->given($current = new \DateTime('2018-11-01 12:10:23+0200'))
            ->given($diff = $this->mock->diff($current))
            ->then
            
            ->boolean($this->mock->humanDateIsYesterdayOrTomorrow($diff, $current))
                ->isFalse()
        ;
    }
    
    public function testHumanParseDateAndTimeText()
    {
        $this->assert('test Helpers\Dates::humanParseDateAndTimeText - without item to parse')
            ->given($parsedTxt = new class {
                public $date = 'test';
                public $time = 'test';
            })
            ->variable($this->mock->humanParseDateAndTimeText($parsedTxt, '2018-10-31', '17:13'))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo('test')
            ->string($parsedTxt->time)
                ->isEqualTo('test')
        ;
        
        $this->assert('test Helpers\Dates::humanParseDateAndTimeText - with item to parse')
            ->given($parsedTxt = new class {
                public $date = 'the {date}';
                public $time = ' at {time}';
            })
            ->variable($this->mock->humanParseDateAndTimeText($parsedTxt, '2018-10-31', '17:13'))
                ->isNull()
            ->string($parsedTxt->date)
                ->isEqualTo('the 2018-10-31')
            ->string($parsedTxt->time)
                ->isEqualTo(' at 17:13')
        ;
    }
}
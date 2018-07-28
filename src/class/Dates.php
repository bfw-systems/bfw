<?php

namespace BFW;

use \DateTime;
use \Exception;

/**
 * Class to have shortcuts to DateTime(Zone) methods and to display a date
 * with words and not only numbers (today, yesterday, since... etc).
 */
class Dates extends DateTime
{
    /**
     * @const ERR_MODIFY_PATTERN_NOT_MATCH Exception code if the pattern used
     * into the method modify() not match with the regex.
     */
    const ERR_MODIFY_PATTERN_NOT_MATCH = 1306001;
    
    /**
     * @const ERR_MODIFY_UNKNOWN_MODIFIER Exception code if the modifier used
     * into the method modify() is unknown.
     */
    const ERR_MODIFY_UNKNOWN_MODIFIER = 1306002;
    
    /**
     * @var string[] $humanReadableI18n Words used in method to transform
     *  date difference to human readable.
     */
    protected static $humanReadableI18n = [
        'now'       => 'now',
        'since'     => 'since',
        'in'        => 'in',
        'yesterday' => 'yesterday',
        'tomorrow'  => 'tomorrow',
        'the'       => 'the',
        'at'        => 'at'
    ];

    /**
     * @var string[] $humanReadableFormats Date and time formats used in
     *  method to transform date difference to human readable.
     */
    protected static $humanReadableFormats = [
        'dateSameYear'      => 'm-d',
        'dateDifferentYear' => 'Y-m-d',
        'time'              => 'H:i'
    ];
    
    /**
     * @var string[] $modifyNewKeywords Add new keywords which can be used
     *  with the modify method. The key is the new keyword and the value the
     *  corresponding keyword into DateTime::modify method.
     */
    protected static $modifyNewKeywords = [];

    /**
     * Return the value of the humanReadableI18n property
     * 
     * @return string[]
     */
    public static function getHumanReadableI18n()
    {
        return self::$humanReadableI18n;
    }

    /**
     * Define a new value for a key of the humanReadableI18n property
     * 
     * @param string $key The key in humanReadableI18n
     * @param string $value The new value for the key
     * 
     * @return void
     */
    public static function setHumanReadableI18nKey($key, $value)
    {
        self::$humanReadableI18n[$key] = $value;
    }

    /**
     * Define a new value to the property humanReadableI18n
     * 
     * @param string[] $value The new value for the property
     * 
     * @return void
     */
    public static function setHumanReadableI18n($value)
    {
        self::$humanReadableI18n = $value;
    }

    /**
     * Return the value of the humanReadableFormats property
     * 
     * @return string[]
     */
    public static function getHumanReadableFormats()
    {
        return self::$humanReadableFormats;
    }

    /**
     * Define a new value for a key of the humanReadableFormats property
     * 
     * @param string $key The key in humanReadableFormats
     * @param string $value The new value for the key
     * 
     * @return void
     */
    public static function setHumanReadableFormatsKey($key, $value)
    {
        self::$humanReadableFormats[$key] = $value;
    }

    /**
     * Define a new value to the property humanReadableFormats
     * 
     * @param string[] $value The new value for the property
     * 
     * @return void
     */
    public static function setHumanReadableFormats($value)
    {
        self::$humanReadableFormats = $value;
    }
    
    /**
     * Return the value of the modifyNewKeywords property
     * 
     * @return string[]
     */
    public static function getModifyNewKeywords()
    {
        return self::$modifyNewKeywords;
    }
    
    /**
     * Define a new value to the property modifyNewKeywords
     * 
     * @param string[] $value The new value for the property
     * 
     * @return void
     */
    public static function setModifyNewKeywords($value)
    {
        self::$modifyNewKeywords = $value;
    }

    /**
     * Return the date. Format is Y-m-d H:i:sO
     * 
     * @return string
     */
    public function getDate()
    {
        return parent::format('Y-m-d H:i:sO');
    }

    /**
     * Return a numeric representation of a year, 4 digits.
     * 
     * @return int
     */
    public function getYear()
    {
        return (int) parent::format('Y');
    }

    /**
     * Return the numeric representation of a month, without leading zeros.
     * The returned int format can not have leading zeros.
     * 
     * @return int
     */
    public function getMonth()
    {
        return (int) parent::format('m');
    }

    /**
     * Return the day of the month without leading zeros.
     * The returned int format can not have leading zeros.
     * 
     * @return int
     */
    public function getDay()
    {
        return (int) parent::format('d');
    }

    /**
     * Return 24-hour format without leading zeros.
     * The returned int format can not have leading zeros.
     * 
     * @return int
     */
    public function getHour()
    {
        return (int) parent::format('H');
    }

    /**
     * Return minutes, without leading zeros.
     * The returned int format can not have leading zeros.
     * 
     * @return int
     */
    public function getMinute()
    {
        return (int) parent::format('i');
    }

    /**
     * Return second, without leading zeros.
     * The returned int format can not have leading zeros.
     * 
     * @return int
     */
    public function getSecond()
    {
        return (int) parent::format('s');
    }

    /**
     * Return the difference to Greenwich time (GMT)
     * with colon between hours and minutes
     * 
     * @return string
     */
    public function getZone()
    {
        return parent::format('P');
    }

    /**
     * Override modify DateTime method to allow personal keywords
     * 
     * @param string $modify A date/time string
     * 
     * @return \BFW\Dates
     */
    public function modify($modify)
    {
        $originalDate = clone $this;
        @parent::modify($modify); //Yeurk, but for personnal pattern, no choice
        //Maybe with try/catch for PHP7, but for 5.6, no idea to avoid that.

        //If the keyword used is ok with DateTime::modify method
        if ($originalDate != $this) {
            return $this;
        }

        $this->modifyWithOthersKeywords($modify);

        return $this;
    }

    /**
     * Get DateTime equivalent keyword for a personal keyword declared into
     * the property modifyNewKeywords.
     * 
     * @return \stdClass
     */
    protected function obtainNewKeywordsForModify()
    {
        $search  = [];
        $replace = [];
        
        foreach (self::$modifyNewKeywords as $searchKey => $replaceKey) {
            $search[]  = $searchKey;
            $replace[] = $replaceKey;
        }
        
        return (object) [
            'search'  => $search,
            'replace' => $replace
        ];
    }
    
    /**
     * Use personal keyword on modify method
     * 
     * @param string $modify A date/time string
     * 
     * @throws Exception If bad pattern or unknown keyword
     */
    protected function modifyWithOthersKeywords($modify)
    {
        $keywords = $this->obtainNewKeywordsForModify();
        $match    = [];
        
        //Regex on the $modify parameter to get the used keyword
        if (preg_match('#(\+|\-)([0-9]+) ([a-z]+)#i', $modify, $match) !== 1) {
            throw new Exception(
                'Dates::modify pattern not match.',
                $this::ERR_MODIFY_PATTERN_NOT_MATCH
            );
        }
        
        $keyword = str_replace(
            $keywords->search,
            $keywords->replace,
            strtolower($match[3])
        );
        
        $originalDate = clone $this;
        //Yeurk, but I preferer sends an Exception, not an error.
        //Maybe easier PHP7 (try/catch ?), but no idea for 5.6.
        @parent::modify($match[1].$match[2].' '.$keyword);
        
        //If no change on object, The keyword is unknown
        if ($originalDate == $this) {
            throw new Exception(
                'Dates::modify Parameter '.$match[3].' is unknown.',
                $this::ERR_MODIFY_UNKNOWN_MODIFIER
            );
        }
    }
    
    /**
     * Return date's SQL format (postgresql format).
     * The return can be an array or a string.
     * 
     * @param boolean $returnArray (default false) True to return an array.
     * @param boolean $withZone (default false) True to include the timezone
     *  into the time returned data.
     * 
     * @return string[]|string
     */
    public function getSqlFormat($returnArray = false, $withZone = false)
    {
        $date = $this->format('Y-m-d');
        $time = $this->format('H:i:s');
        
        if ($withZone === true) {
            $time .= $this->format('O');
        }

        if ($returnArray) {
            return [$date, $time];
        }

        return $date.' '.$time;
    }

    /**
     * List all timezone existing in current php version
     * 
     * @return string[]
     */
    public function lstTimeZone()
    {
        return parent::getTimezone()->listIdentifiers();
    }

    /**
     * List all continent define in php DateTimeZone.
     * 
     * @return string[]
     */
    public function lstTimeZoneContinent()
    {
        return [
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
        ];
    }

    /**
     * List all available country for a continent
     * 
     * @param string $continent The continent for which we want
     *  the countries list
     * 
     * @return string[]
     */
    public function lstTimeZoneCountries($continent)
    {
        $allCountries = $this->lstTimeZone();
        $countries    = [];

        foreach ($allCountries as $country) {
            if (strpos($country, $continent) !== false) {
                $countries[] = $country;
            }
        }

        return $countries;
    }

    /**
     * Transform a date to a human readable format
     * 
     * @param boolean $returnDateAndTime (default true) True to return date and
     *  time concatenated with a space. False to have only date.
     * 
     * @return string
     */
    public function humanReadable($returnDateAndTime = true)
    {
        $current = new Dates;
        $diff    = parent::diff($current);
        
        $parsedTxt = (object) [
            'date' => '',
            'time' => ''
        ];

        if ($current == $this) {
            //Now
            $this->humanDateNow($parsedTxt);
        } elseif ($diff->d === 1 && $diff->m === 0 && $diff->y === 0) {
            if ($diff->invert === 0) {
                $this->humanDateYesterday($parsedTxt); //Yesterday
            } else {
                $this->humanDateTomorrow($parsedTxt); //Tomorrow
            }
        } elseif ($diff->days === 0) {
            //Today
            $this->humanDateToday($parsedTxt, $diff);
        } else {
            $this->humanDateOther($parsedTxt, $current);
        }

        $txtReturned = $parsedTxt->date;
        if ($returnDateAndTime === true && $parsedTxt->time !== '') {
            $txtReturned .= ' '.$parsedTxt->time;
        }

        return $txtReturned;
    }
    
    /**
     * Format date to human readable when the date is now
     * 
     * @param \stdClass $parsedTxt Texts returned by humanReadable method
     * 
     * @return void
     */
    protected function humanDateNow($parsedTxt)
    {
        $currentClass    = get_called_class();
        $parsedTxt->date = $currentClass::$humanReadableI18n['now'];
    }
    
    /**
     * Format date to human readable when date is today
     * 
     * @param \stdClass $parsedTxt Texts returned by humanReadable method
     * @param \DateInterval $diff Interval between now and date to read
     * 
     * @return void
     */
    protected function humanDateToday($parsedTxt, $diff)
    {
        $textKey = 'since';
        if ($diff->invert === 1) {
            $textKey = 'in';
        }
        
        $currentClass    = get_called_class();
        $parsedTxt->date = $currentClass::$humanReadableI18n[$textKey].' ';

        if ($diff->h === 0 && $diff->i === 0) {
            $parsedTxt->date .= $diff->s.'s';
        } elseif ($diff->h === 0) {
            $parsedTxt->date .= $diff->i.'min';
        } else {
            $parsedTxt->date .= $diff->h.'h';
        }
    }
    
    /**
     * Format date to human readable when date is yesterday
     * 
     * @param \stdClass $parsedTxt Texts returned by humanReadable method
     * 
     * @return void
     */
    protected function humanDateYesterday($parsedTxt)
    {
        $currentClass    = get_called_class();
        $parsedTxt->date = $currentClass::$humanReadableI18n['yesterday'];
        $parsedTxt->time = $currentClass::$humanReadableI18n['at']
            .' '
            .$this->format(
                $currentClass::$humanReadableFormats['time']
            );
    }
    
    /**
     * Format date to human readable when date is tomorrow
     * 
     * @param \stdClass $parsedTxt Texts returned by humanReadable method
     * 
     * @return void
     */
    protected function humanDateTomorrow($parsedTxt)
    {
        $currentClass    = get_called_class();
        $parsedTxt->date = $currentClass::$humanReadableI18n['tomorrow'];
        $parsedTxt->time = $currentClass::$humanReadableI18n['at']
            .' '
            .$this->format(
                $currentClass::$humanReadableFormats['time']
            );
    }
    
    /**
     * Format date to human readable when date is not now, today or yesterday
     * 
     * @param \stdClass $parsedTxt Texts returned by humanReadable method
     * @param \DateTime $current DateTime object for now
     * 
     * @return void
     */
    protected function humanDateOther($parsedTxt, $current)
    {
        $currentClass = get_called_class();
        
        $dateFormat = $currentClass::$humanReadableFormats['dateDifferentYear'];
        if ($current->format('Y') === $this->format('Y')) {
            $dateFormat = $currentClass::$humanReadableFormats['dateSameYear'];
        }

        $parsedTxt->date = $currentClass::$humanReadableI18n['the']
            .' '
            .$this->format($dateFormat);

        $parsedTxt->time = $currentClass::$humanReadableI18n['at']
            .' '
            .$this->format(
                $currentClass::$humanReadableFormats['time']
            );
    }
}

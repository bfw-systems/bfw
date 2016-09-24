<?php

namespace BFW;

use \DateTime;
use \Exception;

/**
 * Class for use easier DateTime
 */
class Dates extends DateTime
{
    /**
     * @var string[] $humainReadableI18n Words used in method to transform
     *      date difference to humain readable.
     */
    protected static $humainReadableI18n = [
        'now'       => 'now',
        'since'     => 'since',
        'yesterday' => 'yesterday',
        'the'       => 'the',
        'at'        => 'at'
    ];

    /**
     * @var string[] $humainReadableFormats Date and time format used in
     *      method to transform date difference to humain readable.
     */
    protected static $humainReadableFormats = [
        'dateSameYear'      => 'm-d',
        'dateDifferentYear' => 'Y-m-d',
        'time'              => 'H:i'
    ];

    /**
     * Return attribute humainReadableI18n value
     * 
     * @return string[]
     */
    public static function getHumainReadableI18n()
    {
        return self::$humainReadableI18n;
    }

    /**
     * Define new value to a key in attribute humainReadableI18n
     * 
     * @param string $key The key in humainReadableI18n
     * @param string $value The new value for key
     * 
     * @return void
     */
    public static function setHumainReadableI18nKey($key, $value)
    {
        self::$humainReadableI18n[$key] = $value;
    }

    /**
     * Define new value to the attribute humainReadableI18n
     * 
     * @param string[] $value The new value for attribute
     * 
     * @return void
     */
    public static function setHumainReadableI18n($value)
    {
        self::$humainReadableI18n = $value;
    }

    /**
     * Return attribute humainReadableFormats value
     * 
     * @return string[]
     */
    public static function getHumainReadableFormats()
    {
        return self::$humainReadableFormats;
    }

    /**
     * Define new value to a key in attribute humainReadableFormats
     * 
     * @param string $key The key in humainReadableFormats
     * @param string $value The new value for key
     * 
     * @return void
     */
    public static function setHumainReadableFormatsKey($key, $value)
    {
        self::$humainReadableFormats[$key] = $value;
    }

    /**
     * Define new value to the attribute humainReadableFormats
     * 
     * @param string[] $value The new value for attribute
     * 
     * @return void
     */
    public static function setHumainReadableFormats($value)
    {
        self::$humainReadableFormats = $value;
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
     * Return a full numeric representation of a year, 4 digits
     * 
     * @return int
     */
    public function getYear()
    {
        return (int) parent::format('Y');
    }

    /**
     * Return the numeric representation of a month, with leading zeros
     * 
     * @return int
     */
    public function getMonth()
    {
        return (int) parent::format('m');
    }

    /**
     * Return the day of the month, 2 digits with leading zeros
     * 
     * @return int
     */
    public function getDay()
    {
        return (int) parent::format('d');
    }

    /**
     * Return 24-hour format with leading zeros. 2 digits
     * 
     * @return int
     */
    public function getHour()
    {
        return (int) parent::format('H');
    }

    /**
     * Return minutes, with leading zeros. 2 digits
     * 
     * @return int
     */
    public function getMinute()
    {
        return (int) parent::format('i');
    }

    /**
     * Return second, with leading zeros. 2 digits
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
     * Override modify DateTime method to allow personnal keyword
     * 
     * @param string $modify A date/time string
     * 
     * @return \BFW\Dates
     */
    public function modify($modify)
    {
        $dateDepart = clone $this;
        @parent::modify($modify); //Yeurk, but for personnal pattern, no choice

        if ($dateDepart != $this) {
            return $this;
        }

        $this->modifyOthersKeywords($modify);

        return $this;
    }

    /**
     * Get DateTime equivalent keyword for a personal keyword
     * 
     * @return \stdClass
     */
    protected function getModifyOthersKeywors()
    {
        //Liste des possibilités qu'on permet
        $search = [
            'an', 'ans',
            'mois',
            'jour', 'jours',
            'heure', 'heures',
            'minutes',
            'seconde', 'secondes'
        ];

        //Liste des équivalent pour la fonction modify de DateTime
        $replace = [
            'year', 'year',
            'month',
            'day', 'day',
            'hour', 'hour',
            'minute',
            'second', 'second'
        ];
        
        return (object) [
            'search' => $search,
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
    protected function modifyOthersKeywords($modify)
    {
        $keywords = $this->getModifyOthersKeywors();
        $match    = [];
        
        //Regex sur le paramètre pour récupéré le type de modification
        if (preg_match('#(\+|\-)([0-9]+) ([a-z]+)#i', $modify, $match) !== 1) {
            throw new Exception('Dates::modify pattern not match.');
        }
        
        $keyword = str_replace(
            $keywords->search,
            $keywords->replace,
            strtolower($match[3])
        );
        
        $dateDepart = clone $this;
        //Yeurk, but I preferer sends an Exception, not an error
        @parent::modify($match[1].$match[2].' '.$keyword);
        
        if ($dateDepart == $this) {
            throw new Exception(
                'Dates::modify Parameter '.$match[3].' is unknown.'
            );
        }
    }
    
    /**
     * Return date's SQL format (postgresql format).
     * The return Must be an array or a string.
     * 
     * @param boolean $returnArray (default false) True to return an array.
     * 
     * @return string[]|string
     */
    public function getSqlFormat($returnArray = false)
    {
        $date  = $this->format('Y-m-d');
        $heure = $this->format('H:i:s');

        if ($returnArray) {
            return [$date, $heure];
        }

        return $date.' '.$heure;
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
        ];
    }

    /**
     * Liste des pays possible pour un continent donné
     * 
     * @param string $continent Le continent dans lequel on veux la liste des pays
     * 
     * @return array La liste des pays pour le continent donné
     */
    
    /**
     * List all available country for a continent
     * 
     * @param string $continent
     * 
     * @return string[]
     */
    public function lstTimeZonePays($continent)
    {
        $lst_all = $this->lstTimeZone();
        $return  = [];

        foreach ($lst_all as $val) {
            $pos = strpos($val, $continent);

            if ($pos !== false) {
                $return[] = $val;
            }
        }

        return $return;
    }

    /**
     * Transform a date to a format humain readable
     * 
     * @param boolean $returnDateAndTime (default true) True to return date and
     *      time concat with a space. False to have only date.
     * 
     * @return string
     */
    public function humainReadable($returnDateAndTime = true)
    {
        $actual = new Dates;
        $diff   = parent::diff($actual);

        $returnTxt = (object) [
            'date' => '',
            'time' => ''
        ];

        if ($actual == $this) {
            //A l'instant
            $this->humainDateNow($returnTxt);
        } elseif ($diff->d === 1 && $diff->m === 0 && $diff->y === 0) {
            //Hier
            $this->humainDateYesterday($returnTxt);
        } elseif ($diff->days === 0) {
            //Aujourd'hui
            $this->humainDateToday($returnTxt, $diff);
        } else {
            $this->humainDateOther($returnTxt, $actual);
        }

        $txtReturn = $returnTxt->date;
        if ($returnDateAndTime === true && $returnTxt->time !== '') {
            $txtReturn .= ' '.$returnTxt->time;
        }

        return $txtReturn;
    }
    
    /**
     * Format date to humain readable when date is now
     * 
     * @param \stdClas $returnTxt Text returned by humainReadable
     * 
     * @return void
     */
    protected function humainDateNow(&$returnTxt)
    {
        $currentClass    = get_called_class();
        $returnTxt->date = $currentClass::$humainReadableI18n['now'];
    }
    
    /**
     * Format date to humain readable when date is today
     * 
     * @param \stdClas $returnTxt Text returned by humainReadable
     * @param \DateInterval $diff Interval between now and date to read
     * 
     * @return void
     */
    protected function humainDateToday(&$returnTxt, $diff)
    {
        $currentClass    = get_called_class();
        $returnTxt->date = $currentClass::$humainReadableI18n['since'].' ';

        if ($diff->h === 0 && $diff->i === 0) {
            $returnTxt->date .= $diff->s.'s';
        } elseif ($diff->h === 0) {
            $returnTxt->date .= $diff->i.'min';
        } else {
            $returnTxt->date .= $diff->h.'h';
        }
    }
    
    /**
     * Format date to humain readable when date is yesterday
     * 
     * @param \stdClas $returnTxt Text returned by humainReadable
     * 
     * @return void
     */
    protected function humainDateYesterday(&$returnTxt)
    {
        $currentClass    = get_called_class();
        $returnTxt->date = $currentClass::$humainReadableI18n['yesterday'];
        $returnTxt->time = $currentClass::$humainReadableI18n['at']
            .' '
            .$this->format(
                $currentClass::$humainReadableFormats['time']
            );
    }
    
    /**
     * Format date to humain readable when date is not now, today or yesterday
     * 
     * @param \stdClas $returnTxt Text returned by humainReadable
     * @param \DateTime $actual DateTime object for now
     * 
     * @return void
     */
    protected function humainDateOther(&$returnTxt, $actual)
    {
        $currentClass = get_called_class();
        
        $dateFormat = $currentClass::$humainReadableFormats['dateDifferentYear'];
        if ($actual->format('Y') === $this->format('Y')) {
            $dateFormat = $currentClass::$humainReadableFormats['dateSameYear'];
        }

        $returnTxt->date = $currentClass::$humainReadableI18n['the']
            .' '
            .$this->format($dateFormat);

        $returnTxt->time = $currentClass::$humainReadableI18n['at']
            .' '
            .$this->format(
                $currentClass::$humainReadableFormats['time']
            );
    }
}

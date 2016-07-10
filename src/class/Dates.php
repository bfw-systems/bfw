<?php

namespace BFW;

use \DateTime;
use \Exception;

class Dates extends DateTime
{
    protected static $humainReadableI18n = [
        'now'       => 'Now',
        'since'     => 'Since',
        'yesterday' => 'Testerday',
        'the'       => 'The',
        'at'        => 'at'
    ];
    
    protected static $humainReadableFormats = [
        'dateSameYear'      => 'm-d',
        'dateDifferentYear' => 'Y-m-d',
        'time'              => 'H:i'
    ];
    
    public static function getHumainReadableI18n()
    {
        return self::$humainReadableI18n;
    }
    
    public static function setHumainReadableI18nKey($key, $value)
    {
        self::$humainReadableI18n[$key] = $value;
    }
    
    public static function setHumainReadableI18n($value)
    {
        self::$humainReadableI18n = $value;
    }
    
    public static function getHumainReadableFormats()
    {
        return self::$humainReadableFormats;
    }
    
    public static function setHumainReadableFormatsKey($key, $value)
    {
        self::$humainReadableFormats[$key] = $value;
    }
    
    public static function setHumainReadableFormats($value)
    {
        self::$humainReadableFormats = $value;
    }
    
    /**
     * Accesseur vers l'attribut $date
     */
    public function getDate()
    {
        return parent::format('Y-m-d H:i:sO');
    }

    public function getYear()
    {
        return parent::format('Y');
    }

    public function getMonth()
    {
        return parent::format('m');
    }

    public function getDay()
    {
        return parent::format('d');
    }

    public function getHour()
    {
        return parent::format('H');
    }

    public function getMinute()
    {
        return parent::format('i');
    }

    public function getSecond()
    {
        return parent::format('s');
    }

    public function getZone()
    {
        return parent::format('P');;
    }
    
    public function modify($modify)
    {
        $dateDepart = clone $this;
        parent::modify($modify);
        
        if($dateDepart == $this) {
            return $this;
        }
        
        $this->modifyOthersKeywords($modify);
        
        return $this;
    }
    
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
    
    protected function modifyOthersKeywords($modify)
    {
        $keywords = $this->getModifyOthersKeywors();
        $match    = [];
        
        //Regex sur le paramètre pour récupéré le type de modification
        if(preg_match('#(\+|\-)([0-9]+) ([a-z]+)#i', $modify, $match) !== 1) {
            throw new Exceptio('Date::modify pattern not match.');
        }
        
        $keyword = str_replace(
            $keywords->search,
            $keywords->replace,
            strtolower($match[3])
         );
        
        $dateDepart = clone $this;
        parent::modify($match[1].$match[2].' '.$keyword);
        
        if($dateDepart == $this) {
            throw new Exception('Parameter '.$match[3].' is unknown.');
        }
    }
    
    /**
     * Renvoi au format pour SQL (postgresql) via un array
     * 
     * @param bool $returnArray (default: false) Indique si on veux retourner 
     * un string ayant tout, ou un array ayant la date et l'heure séparé
     * 
     * @return string|array Le format pour SQL
     * Si string : aaaa-mm-jj hh:mm:ss
     * Si array : [0]=>partie date (aaaa-mm-jj), [1]=>partie heure (hh:mm:ss)
     */
    public function getSql($returnArray = false)
    {
        $date  = $this->format('Y-m-d');
        $heure = $this->format('H:i:s');
        
        if($returnArray) {
            return [$date, $heure];
        }
        
        return $date.' '.$heure;
    }
    
    /**
     * Liste tous les timezone qui existe
     * 
     * @return array La liste des timezone possible
     */
    public function lst_TimeZone()
    {
        return parent::getTimezone()->listIdentifiers();
    }
    
    /**
     * Liste les continents possible pour les timezones
     * 
     * @return string[] La liste des continents
     */
    public function lst_TimeZoneContinent()
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
    public function lst_TimeZonePays($continent)
    {
        $lst_all = $this->lst_TimeZone();
        $return  = [];
        
        $pos = false;
        foreach($lst_all as $val) {
            $pos = strpos($val, $continent);
            
            if($pos !== false) {
                $return[] = $val;
            }
        }
        
        return $return;
    }
    
    public function humainReadable($returnDateAndTime = true, $toLower = false)
    {
        $actual      = new Dates;
        $diff        = parent::diff($actual);
        
        $returnTxt = (object) [
            'date' => '',
            'time' => ''
        ];
        
        if($actual == $this) {
            //A l'instant
            
            $returnTxt->date = self::$humainReadableI18n['now'];
        } elseif($actual->format('d') !== parent::format('d')) {
            //Hier
            
            $returnTxt->date = self::$humainReadableI18n['yesterday'];
            $returnTxt->time = self::$humainReadableI18n['at']
                                .' '
                                .parent::format(
                                    self::$humainReadableFormats['time']
                                );
        } elseif($diff->days === 0) {
            //Aujourd'hui
            
            $returnTxt->date = self::$humainReadableI18n['since'];
            
            if($diff->h === 0 && $diff->m === 0) {
                $returnTxt->date .= $diff->s.'s';
            } elseif($diff->h === 0) {
                $returnTxt->date .= $diff->s.'min';
            } else {
                $returnTxt->date .= $diff->h.'h';
            }
        } else {
            $dateFormat = self::$humainReadableFormats['dateDifferentYear'];
            if($actual->format('Y') === parent::format('Y')) {
                $dateFormat = self::$humainReadableFormats['dateSameYear'];
            }
            
            
            $returnTxt->date = self::$humainReadableI18n['the']
                                .' '
                                .parent::format($dateFormat);
            
            $returnTxt->time = self::$humainReadableI18n['at']
                                .' '
                                .parent::format(
                                    self::$humainReadableFormats['time']
                                );
        }
        
        $txtReturn = $returnTxt->date;
        if($returnDateAndTime === true && $returnTxt->time !== '') {
            $txtReturn .= ' '.$returnTxt->time;
        }
        
        if($toLower === true) {
            $txtReturn = mb_strtolower($txtReturn);
        }
        
        return $txtReturn;
    }
}

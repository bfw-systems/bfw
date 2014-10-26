<?php
/**
 * Classes en rapport avec les Dates
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFW;

use \Exception;

/**
 * Classe de gestion des dates
 * Le format de la date est aaaa-mm-jj hh:mm:ss+OO:OO
 * @package bfw
 */
class Date extends \DateTime implements \BFWInterface\IDate
{
    /**
     * @var \BFW\Kernel $_kernel L'instance du Kernel
     */
    protected $_kernel;
    
    /**
     * @var string $date La date au format string
     */
    protected $date = '';
    
    /**
     * @var string $annee L'année de la date
     */
    protected $annee = '';
    
    /**
     * @var string $mois Le mois de la date
     */
    protected $mois = '';
    
    /**
     * @var string $jour Le jour de la date
     */
    protected $jour = '';
    
    /**
     * @var string $heure L'heure de la date
     */
    protected $heure = '';
    
    /**
     * @var string $minute Les minutes de la date
     */
    protected $minute = '';
    
    /**
     * @var string $seconde Les secondes de la date
     */
    protected $seconde = '';
    
    /**
     * @var string $zone Le timezone à utiliser
     */
    protected $zone;
    
    /**
     * Accesseur vers l'attribut $date
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * Accesseur vers l'attribut $annee
     */
    public function getAnnee()
    {
        return $this->annee;
    }
    
    /**
     * Accesseur vers l'attribut $mois
     */
    public function getMois()
    {
        return $this->mois;
    }
    
    /**
     * Accesseur vers l'attribut $jour
     */
    public function getJour()
    {
        return $this->jour;
    }
    
    /**
     * Accesseur vers l'attribut $heure
     */
    public function getHeure()
    {
        return $this->heure;
    }
    
    /**
     * Accesseur vers l'attribut $minute
     */
    public function getMinute()
    {
        return $this->minute;
    }
    
    /**
     * Accesseur vers l'attribut $seconde
     */
    public function getSeconde()
    {
        return $this->seconde;
    }
    
    /**
     * Accesseur vers l'attribut $zone
     */
    public function getZone()
    {
        return $this->zone;
    }
    
    /**
     * Constructeur
     * La date dans un format précis (aaaa-mm-jj hh:mm:ss+OO:OO)
     * S'il n'y a pas ":00" à la fin, alors c'est géré.
     * 
     * @param string $date (default: "now") La date sur laquelle travailler. Si pas indiqué, il s'agit de la date actuelle.
     */
    public function __construct($date="now")
    {
        $this->_kernel = getKernel();
        
        if($date != "now")
        {
            $len = strlen($date);
            $len -= 3;
            if($date[$len] == '+')
            {
                $date .= ':00';
            }
        }
        
        $this->date = $date;
        parent::__construct($date);
        
        if($date == "now")
        {
            $this->date = $this->format('Y-m-d H:i:sO');
        }
        
        $this->MAJ_Attributes();
    }
    
    /**
     * Initialise les attributs avec leurs valeurs respectives. La méthode est appelé dans le constructeur
     */
    final private function MAJ_Attributes()
    {
        $this->annee = parent::format('Y');
        $this->mois = parent::format('m');
        $this->jour = parent::format('d');
        $this->heure = parent::format('H');
        $this->minute = parent::format('i');
        $this->seconde = parent::format('s');
        $this->zone = parent::format('P');
        
        $this->date = parent::format('Y-m-d H:i:sO');
    }
    
    /**
     * Modifie une données de la date
     * 
     * @param string $cond La partie à modifier : year, mouth, day, jour, minute, second
     * 
     * @throws \Exception : Si le paramètre pour modifier n'est pas géré
     * 
     * @return \BFW\Date : Retourne l'objet si la modification à réussi.
     */
    public function modify($cond)
    {
        //Date actuel
        $dateOri = parent::format('Y-m-d H:i:s');
        
        //On tente la modif avec le paramètre de la fonction
        $mod = @parent::modify($cond); //@ pour éviter une erreur car on permet d'autres format
        $dateMod = parent::format('Y-m-d H:i:s');
        
        //Si la modif à marcher direct, on met à jour et on sort.
        if(!($dateOri == $dateMod || $mod == false))
        {
            $this->MAJ_Attributes();
            return $this;
        }
        
        $match = array();
        //Regex sur le paramètre pour récupéré le type de modification
        preg_match('#(\+|\-)([0-9]+) ([a-z]+)#i', $cond, $match);
        $match[3] = strtolower($match[3]);
        
        //Liste des possibilités qu'on permet
        $search = array(
            'an', 'ans',
            'mois',
            'jour', 'jours',
            'heure', 'heures',
            'minutes',
            'seconde', 'secondes'
        );
        
        //Liste des équivalent pour la fonction modify de DateTime
        $replace = array(
            'year', 'year',
            'month',
            'day', 'day',
            'hour', 'hour',
            'minute',
            'second', 'second'
        );
        
        //On remplace le type de modification par sa valeur pour DateTime
        $real = str_replace($search, $replace, $match[3]);
        
        //Et on retente la modif
        $mod2 = @parent::modify($match[1].$match[2].' '.$real);
        $dateMod2 = parent::format('Y-m-d H:i:s');
        
        //Si la modif à fail : création d'une exception
        if($dateOri == $dateMod2 || $mod2 == false)
        {
            throw new Exception('Parameter '.$match[3].' is unknown.');
        }
        
        //Maj des attributs et retourne l'instance courante.
        $this->MAJ_Attributes();
        return $this;
    }
    
    /**
     * Renvoi au format pour SQL (postgresql) via un array
     * 
     * @param bool $decoupe (default: false) Indique si on veux retourner un string ayant tout, ou un array ayant la date et l'heure séparé
     * 
     * @return string|array Le format pour SQL
     * Si string : aaaa-mm-jj hh:mm:ss
     * Si array : [0]=>partie date (aaaa-mm-jj), [1]=>partie heure (hh:mm:ss)
     */
    public function getSql($decoupe=false)
    {
        $dateSql = new \DateTime($this->date, new \DateTimeZone(self::ZONE_DEFAULT));
        $date = $dateSql->format('Y-m-d');
        $heure = $dateSql->format('H:i:s');
        
        if($decoupe) {return array($date, $heure);}
        
        return $date.' '.$heure;
    }
    
    /**
     * Modifie le timezone
     * 
     * @param string $NewZone le nouveau time zone
     */
    public function setZone($NewZone)
    {
        parent::setTimezone(new \DateTimeZone($NewZone));
    }
    
    /**
     * Liste tous les timezone qui existe
     * 
     * @return array La liste des timezone possible
     */
    public function lst_TimeZone()
    {
        $TimeZone = parent::getTimezone();
        return $TimeZone->listIdentifiers();
    }
    
    /**
     * Liste les continents possible pour les timezones
     * 
     * @return string[] La liste des continents
     */
    public function lst_TimeZoneContinent()
    {
        $lst_continent = array(
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
        );
        
        return $lst_continent;
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
        $return = array();
        
        $pos = false;
        foreach($lst_all as $val)
        {
            $pos = strpos($val, $continent);
            if($pos !== false)
            {
                $return[] = $val;
            }
        }
        
        return $return;
    }
    
    /**
     * Transforme la date en un format plus facilement lisible.
     * 
     * Paramètre en entrée
     *      $tout :
     *          1 : On affiche la date en entier (jour et heure)
     *          0 : On affiche que le jour
     *      $minus : 
     *          1 : On affiche le texte en minuscule (hier, le)
     *          0 : On affiche le texte en normal (Hier, Le)
     * 
     * possibilitées en sortie : 
     *
     *      $tout == 1
     *          Il y 1s
     *          Il y 1min
     *          Il y 1h
     *          Hier à 00:00
     *          Le 00/00 à 00:00
     *          Le 00/00/0000 à 00:00 (si l'année n'est pas la même)
     *      
     *      $tout == 0
     *          Il y 1s
     *          Il y 1min
     *          Il y 1h
     *          Hier
     *          Le 00/00
     *          Le 00/00/0000 (si l'année n'est pas la même)
     *      
     *      Ou "Maintenant" (qu'importe la valeur de $tout)
     * 
     * @param bool $tout  (default: true) Affiche la date en entier (true) ou non (false).
     * @param bool $minus (default: false) Affiche la date en minuscule (true) ou non (false).
     * 
     * @return string La date simplifié
     */
    public function aff_simple($tout=true, $minus=false)
    {
        //Découpage de la date donnée dans l'instance de la classe
        $annee  = $this->annee;
        $mois   = $this->mois;
        $jour   = $this->jour;
        $heure  = $this->heure;
        $minute = $this->minute;
        
        //La date actuelle
        $time = new Date();
        
        
        /*
        Si c'est à l'instant même
            Afficher "Maintenant"
        Si c'est le même jour OU (c'est jour-1 ET différence d'heure < 2h)
            Afficher "il y a xxh"
        Si c'est jour-1 ET différence d'heure > 2h
            Afficher "Hier"
        Sinon
            Afficher au format date
        */
        
        $diff        = parent::diff($time);
        $diffAnnee   = $diff->format('%Y');
        $diffMois    = $diff->format('%M');
        $diffJour    = $diff->format('%D');
        $diffHeure   = $diff->format('%H');
        $diffMinute  = $diff->format('%I');
        $diffSeconde = $diff->format('%S');
        $diffInvert  = $diff->invert;
        
        //@TODO : All $diffXxx variable is on a string type, not int.
        if($diffAnnee == 0 && $diffMois == 0 && $diffJour == 0 && $diffHeure == 0 && $diffMinute == 0 && $diffSeconde == 0)
        {
            $aff_date = 'Maintenant';
            $aff_heure = '';
        }
        elseif($diffAnnee == 0 && $diffMois == 0 && $diffJour == 0 && $diffHeure <= 2)
        {
            $aff_date = 'Il y a '; //On commence par déclaré le début de l'affichage
            
            if($diffJour > 1) //Dans le cas de 23h -> $heure_diff = -1
            {
                $h = 24+$diffHeure; //24 + (-1)
                $aff_date .= $h.'h';
            }
            else
            {
                if($diffHeure > 0)
                {
                    $aff_date .= $diffHeure.'h';
                }
                elseif($diffHeure <= 0 && $diffMinute > 0)
                {
                    $aff_date .= $diffMinute.'min';
                }
                else
                {
                    $aff_date .= $diffSeconde.'s';
                }
            }
            
            $aff_heure = ''; #Partie prévu pour l'affichage
        }
        elseif($diffInvert == 0 && $diffAnnee == 0 && $diffMois == 0 && (($diffJour == 0 && $diffHeure > 2) || ($diffJour == 1 && $diffHeure == 0)))
        {
            //C'était hier
            $aff_date = 'Hier'; #On affiche donc en première partie "hier"
            $aff_heure = ' à '.$heure.':'.$minute; #et en seconde partie, l'heure et les minutes
        }
        else
        {
            //Sinon et bien c'était il y a plus de 48h, et on affiche la date au format habituel
            $aff_date  = 'Le '.$jour.'/'.$mois; //D'abord le jour et le mois
            $aff_heure = ' à '.$heure.':'.$minute; //Et ensuite l'heure et les minutes
            
            //Et si l'année n'est pas la meme que l'actuel, alors on rajoute l'année à la fin de la première partie l'année
            if($diffAnnee != 0) {$aff_date .= '/'.$annee;}
        }
        
        $aff = $aff_date; //On renvoi la date
        //Si on veut tout afficher (la date et l'heure)
        if($tout == 1) {$aff .= $aff_heure;}
        
        //Met la première lettre en minuscule dans le cas où l'ont veuille du minuscule
        if($minus == true) {$aff = mb_strtolower($aff);}
        
        return $aff; //Et on retour la date parser :D
    }
}

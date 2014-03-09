<?php
/**
 * Interface en rapport avec la classe Date
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFWInterface;

/**
 * Interface de la classe Date
 * @package bfw
 */
interface IDate
{
    const ZONE_DEFAULT = 'Europe/Paris'; //Le timeZone par défault
    
    /**
     * Fonction magique, permet de lire les attributs directement
     * 
     * @param string $name Le nom de l'attribut auquel on veux accéder.
     */
    public function __get($name);
    
    /**
     * Constructeur
     * La date dans un format précis (aaaa-mm-jj hh:mm:ss+OO:OO)
     * S'il n'y a pas ":00" à la fin, alors c'est géré.
     * 
     * @param string $date (default: "now") La date sur laquelle travailler. Si pas indiqué, il s'agit de la date actuelle.
     */
    public function __construct($date="now");
    
    /**
     * Modifie une données de la date
     * 
     * @param string $cond La partie à modifier : year, mouth, day, jour, minute, second
     * 
     * @return bool True la si modif à réussi, fales si erreur
     */
    public function modify($cond);
    
    /**
     * Renvoi au format pour SQL (postgresql) via un array
     * 
     * @param bool $decoupe (default: false) Indique si on veux retourner un string ayant tout, ou un array ayant la date et l'heure séparé
     * 
     * @return string|array Le format pour SQL
     * Si string : aaaa-mm-jj hh:mm:ss
     * Si array : [0]=>partie date (aaaa-mm-jj), [1]=>partie heure (hh:mm:ss)
     */
    public function getSql($decoupe=false);
    
    /**
     * Modifie le timezone
     * 
     * @param string $NewZone le nouveau time zone
     */
    public function setZone($NewZone);
    
    /**
     * Liste tous les timezone qui existe
     * 
     * @return array La liste des timezone possible
     */
    public function lst_TimeZone();
    
    /**
     * Liste les continents possible pour les timezones
     * 
     * @return array La liste des continents
     */
    public function lst_TimeZoneContinent();
    
    /**
     * Liste des pays possible pour un continent donné
     * 
     * @param string $continent Le continent dans lequel on veux la liste des pays
     * 
     * @return array La liste des pays pour le continent donné
     */
    public function lst_TimeZonePays($continent);
    
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
    public function aff_simple($tout=1, $minus=false);
}
?>
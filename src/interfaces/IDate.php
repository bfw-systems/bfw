<?php
/**
 * Interface en rapport avec la classe Date
 * @author Vermeulen Maxime
 * @version 1.0
 */

namespace BFWInterface;

/**
 * Interface de la classe Date
 * @package BFW
 */
interface IDate
{
    const ZONE_DEFAULT = 'Europe/Paris'; //Le timeZone par défault
    
    /**
     * Fonction magique, permet de lire les attributs directement
     * @param string $name : Le nom de l'attribut auquel on veux accéder.
     */
    public function __get($name);
    
    /**
     * Constructeur
     * La date dans un format précis (aaaa-mm-jj hh:mm:ss+OO:OO)
     * S'il n'y a pas ":00" à la fin, alors c'est géré.
     * @param string $date [opt] : La date sur laquelle travailler. Si pas indiqué, il s'agit de la date actuelle.
     */
    public function __construct($date="now");
    
    /**
     * Modifie une données de la date
     * @param string $cond : La partie à modifier : year, mouth, day, jour, minute, second
     * @return bool : True la si modif à réussi, fales si erreur
     */
    public function modify($cond);
    
    /**
     * Renvoi au format pour SQL (postgresql) via un array
     * @param bool $decoupe [opt=false] : Indique si on veux retourner un string ayant tout, 
     *                                      ou un array ayant la date et l'heure séparé
     * @return string/array : Si string : aaaa-mm-jj hh:mm:ss
     *                        Si array : [0]=>partie date (aaaa-mm-jj), [1]=>partie heure (hh:mm:ss)
     */
    public function getSql($decoupe=false);
    
    /**
     * Modifie le timezone
     * @param string le nouveau time zone
     */
    public function setZone($NewZone);
    
    /**
     * Liste tous les timezone qui existe
     * @return array : La liste des timezone possible
     */
    public function lst_TimeZone();
    
    /**
     * Liste les continents possible pour les timezones
     * @return array : La liste des continents
     */
    public function lst_TimeZoneContinent();
    
    /**
     * Liste des pays possible pour un continent donné
     * @param string : Le continent dans lequel on veux la liste des pays
     * @return array : La liste des pays pour le continent donné
     */
    public function lst_TimeZonePays($continent);
    
    /**
     * Transforme la date en un format plus facilement lisible.
     * @param bool $tout : Affiche la date en entier (true) ou non (false). Par défault "true"
     * @param bool $minus : Affiche la date en minuscule (true) ou non (false). Par défault "false"
     */
    public function aff_simple($tout=1, $minus=false);
}
?>
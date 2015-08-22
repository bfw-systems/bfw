<?php
/**
 * Interface en rapport avec la classe Ram
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFWInterface;

/**
 * Interface de la classe Ram
 * @package bfw
 */
interface IRam
{
    /**
     * Constructeur
     * Se connecte au serveur memcache indiqué
     * 
     * @param string  $host l'host du serveur memcache
     * @param integer $port le port du serveur memcache
     * 
     * @throws Exception : Si l'extension php-memcache n'est présente
     *                     Si les infos sont pas au bon format
     *                     Si la connexion échoue
     * @return void
     */
    public function __construct($host, $port);
    
    /**
     * Permet de stocker une clé en mémoire ou de la mettre à jour
     * 
     * @param string $key    Clé correspondant à la valeur
     * @param mixed  $data   Les nouvelles données. Il n'est pas possible de stocker une valeur de type resource.
     * @param int    $expire (default: 0) Le temps en seconde avant expiration. 0 illimité, max 30jours
     * 
     * @throws \Exception Erreur dsans les paramètres donnée à la méthode
     * 
     * @return bool
     */
    public function setVal($key, $data, $expire=0);
    
    /**
     * On modifie le temps avant expiration des infos sur le serveur memcached pour une clé choisie.
     * 
     * @param string $key la clé disignant les infos concerné
     * @param int    $exp le nouveau temps avant expiration (0: pas d'expiration, max 30jours)
     * 
     * @throws \Exception Erreur dsans les paramètres donnée à la méthode
     * 
     * @return bool
     */
    public function majExpire($key, $exp);
    
    /**
     * Permet de savoir si la clé existe
     * 
     * @param string $key la clé disignant les infos concernées
     * 
     * @throws \Exception Erreur dsans les paramètres donnée à la méthode
     * 
     * @return bool
     */
    public function ifExists($key);
    
    /**
     * Supprime une clé
     * 
     * @param string $key la clé disignant les infos concernées
     * 
     * @return bool
     */
    public function delete($key);
    
    /**
     * Permet de retourner la valeur d'une clé.
     * 
     * @param string $key Clé correspondant à la valeur
     * 
     * @throws \Exception Erreur dsans les paramètres donnée à la méthode
     * 
     * @return mixed La valeur demandée
     */
    public function getVal($key);
}
?>
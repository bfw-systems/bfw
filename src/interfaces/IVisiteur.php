<?php
/**
 * Interface en rapport avec la classe Visiteur
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFWInterface;

/**
 * Interface de la classe Visiteur
 * @package bfw
 */
interface IVisiteur
{
    /**
     * Accesseur vers l'attribut $idSession
     */
    public function getIdSession();
    
    /**
     * Accesseur vers l'attribut $ip
     */
    public function getIp();
    
    /**
     * Accesseur vers l'attribut $host
     */
    public function getHost();
    
    /**
     * Accesseur vers l'attribut $proxy
     */
    public function getProxy();
    
    /**
     * Accesseur vers l'attribut $proxyIp
     */
    public function getProxyIp();
    
    /**
     * Accesseur vers l'attribut $proxyHost
     */
    public function getProxyHost();
    
    /**
     * Accesseur vers l'attribut $os
     */
    public function getOs();
    
    /**
     * Accesseur vers l'attribut $nav
     */
    public function getNav();
    
    /**
     * Accesseur vers l'attribut $langue
     */
    public function getLangue();
    
    /**
     * Accesseur vers l'attribut $langueInitiale
     */
    public function getLangueInitiale();
    
    /**
     * Accesseur vers l'attribut $proviens
     */
    public function getProviens();
    
    /**
     * Accesseur vers l'attribut $url
     */
    public function getUrl();
    
    /**
     * Accesseur vers l'attribut $bot
     */
    public function getBot();
    
    /**
     * Constructeur
     * Récupère les infos et instancie la session
     * @return void
     */
    public function __construct();
}
?>
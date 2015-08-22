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
     * @return string
     */
    public function getIp();
    
    /**
     * Accesseur vers l'attribut $host
     * @return string
     */
    public function getHost();
    
    /**
     * Accesseur vers l'attribut $proxy
     * @return string
     */
    public function getProxy();
    
    /**
     * Accesseur vers l'attribut $proxyIp
     * @return string
     */
    public function getProxyIp();
    
    /**
     * Accesseur vers l'attribut $proxyHost
     * @return string
     */
    public function getProxyHost();
    
    /**
     * Accesseur vers l'attribut $os
     * @return string
     */
    public function getOs();
    
    /**
     * Accesseur vers l'attribut $nav
     * @return string
     */
    public function getNav();
    
    /**
     * Accesseur vers l'attribut $langue
     * @return string
     */
    public function getLangue();
    
    /**
     * Accesseur vers l'attribut $langueInitiale
     * @return string
     */
    public function getLangueInitiale();
    
    /**
     * Accesseur vers l'attribut $proviens
     * @return string
     */
    public function getProviens();
    
    /**
     * Accesseur vers l'attribut $url
     * @return string
     */
    public function getUrl();
    
    /**
     * Accesseur vers l'attribut $bot
     * @return string
     */
    public function getBot();
    
    /**
     * Constructeur
     * Récupère les infos et instancie la session
     */
    public function __construct();
}

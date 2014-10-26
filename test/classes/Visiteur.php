<?php
/**
 * Fichier de test pour une class
 */

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../common.php');

/**
 * Test de la class Visiteur
 */
class Visiteur extends atoum
{
    /**
     * @var $class : Instance de la class Visiteur
     */
    protected $class;

    /**
     * @var $mock : Instance du mock pour la class Visiteur
     */
    protected $mock;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        //$this->class = new \BFW\Visiteur();
        //$this->mock  = new MockVisiteur();
    }

    /**
     * Test du constructeur : Visiteur()
     */
    public function testVisiteur()
    {
        
    }

    /**
     * Test de la méthode recup_infos()
     */
    public function testRecup_infos()
    {
        
    }

    /**
     * Test de la méthode proxy_detect()
     */
    public function testProxy_detect()
    {
        
    }

    /**
     * Test de la méthode proxy_ip_detect()
     */
    public function testProxy_ip_detect()
    {
        
    }

    /**
     * Test de la méthode proxy_host_detect()
     */
    public function testProxy_host_detect()
    {
        
    }

    /**
     * Test de la méthode real_ip_detect()
     */
    public function testReal_ip_detect()
    {
        
    }

    /**
     * Test de la méthode real_host_detect()
     */
    public function testReal_host_detect()
    {
        
    }

    /**
     * Test de la méthode system_detect()
     */
    public function testSystem_detect()
    {
        
    }

    /**
     * Test de la méthode browser_detect()
     */
    public function testBrowser_detect()
    {
        
    }

    /**
     * Test de la méthode language_detect()
     */
    public function testLanguage_detect()
    {
        
    }

    /**
     * Test de la méthode language_convert($lang='')
     */
    public function testLanguage_convert()
    {
        
    }

    /**
     * Test de la méthode referer_detect()
     */
    public function testReferer_detect()
    {
        
    }

    /**
     * Test de la méthode uri_detect()
     */
    public function testUri_detect()
    {
        
    }

}

/**
 * Mock pour la classe Visiteur
 */
class MockVisiteur extends \BFW\Visiteur
{
    /**
     * Accesseur get
     */
    public function __get($name) {return $this->$name;}

    /**
     * Test de la méthode recup_infos()
     */
    public function recup_infos()
    {
        return parent::recup_infos();
    }

    /**
     * Test de la méthode proxy_detect()
     */
    public function proxy_detect()
    {
        return parent::proxy_detect();
    }

    /**
     * Test de la méthode proxy_ip_detect()
     */
    public function proxy_ip_detect()
    {
        return parent::proxy_ip_detect();
    }

    /**
     * Test de la méthode proxy_host_detect()
     */
    public function proxy_host_detect()
    {
        return parent::proxy_host_detect();
    }

    /**
     * Test de la méthode real_ip_detect()
     */
    public function real_ip_detect()
    {
        return parent::real_ip_detect();
    }

    /**
     * Test de la méthode real_host_detect()
     */
    public function real_host_detect()
    {
        return parent::real_host_detect();
    }

    /**
     * Test de la méthode system_detect()
     */
    public function system_detect()
    {
        return parent::system_detect();
    }

    /**
     * Test de la méthode browser_detect()
     */
    public function browser_detect()
    {
        return parent::browser_detect();
    }

    /**
     * Test de la méthode language_detect()
     */
    public function language_detect()
    {
        return parent::language_detect();
    }

    /**
     * Test de la méthode language_convert($lang='')
     */
    public function language_convert($lang='')
    {
        return parent::language_convert($lang);
    }

    /**
     * Test de la méthode referer_detect()
     */
    public function referer_detect()
    {
        return parent::referer_detect();
    }

    /**
     * Test de la méthode uri_detect()
     */
    public function uri_detect()
    {
        return parent::uri_detect();
    }

}

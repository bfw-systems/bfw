<?php
/**
 * Classes en rapport avec les visiteurs
 * @author Minimix
 * @author Hédoux Julien <sg71master@gmail.com>
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFW;

/**
 * Permet de gérer tout ce qui concerne le visiteur
 * @package bfw
 */
class Visiteur implements \BFWInterface\IVisiteur
{
    /**
     * @var $_kernel L'instance du Kernel
     */
    private $_kernel;
    
    /**
     * @var $Id_Session Id de la session correspondante
     */
    private $Id_Session = null;
    
    /**
     * @var $Session Instance de la classe session
     */
    private $Session = null;
    
    /**
     * @var $Nom_Page Le nom de la page sur laquel il est
     */
    private $Nom_Page = "";
    
    /**
     * @var $Ip Son ip
     */
    private $Ip = "";
    
    /**
     * @var $Host L'hostname du visiteur
     */
    private $Host = "";
    
    /**
     * @var $Proxy S'il passe par un proxy
     */
    private $Proxy = "";
    
    /**
     * @var $Proxy_ip L'ip du proxy
     */
    private $Proxy_ip = "";
    
    /**
     * @var $Proxy_host L'hostname du proxy
     */
    private $Proxy_host = "";
    
    /**
     * @var $OS Son système d'exploitation
     */
    private $OS = "";
    
    /**
     * @var $Nav Son navigateur
     */
    private $Nav = "";
    
    /**
     * @var $Langue Sa langue (n'est pas obligatoirement celle utiliser pour le jeu)
     */
    private $Langue = "";
    
    /**
     * @var $Langue_Initiale Les initiale de la langue
     */
    private $Langue_Initiale = "";
    
    /**
     * @var $Proviens L'url d'où il vient
     */
    private $Proviens = "";
    
    /**
     * @var $Url Son url actuelle
     */
    private $Url = "";
    
    /**
     * @var $Bot S'il s'agit d'un robot
     */
    private $Bot = "";

    /**
     * Accesseur get vers les attributs
     * 
     * @param string $name Le nom de l'attribut
     * 
     * @return mixed La valeur de l'attribut
     */
    public function __get($name)
    {
        return $this->$name;
    }
    
    /**
     * Accesseur set vers les attributs
     * 
     * @param string $name Le nom de l'attribut
     * @param mixed  $val  La nouvelle valeure de l'attribut
     */
    public function __set($name, $val)
    {
        $this->$name = $val;
    }
    
    /**
     * Constructeur
     * Récupère les infos et instancie la session
     */
    public function __construct()
    {
        $this->_kernel = getKernel();
        
        $this->recup_infos();
        if(isset($_SESSION['idSess']))
        {
            $this->Id_Session = $_SESSION['idSess'];
        }
    }
    
    /**
     * Récupère les différentes infos sur le visiteur
     */
    private function recup_infos()
    {
        $this->proxy_detect();
        $this->proxy_ip_detect();
        $this->proxy_host_detect();
        $this->real_ip_detect();
        $this->real_host_detect();
        //$this->port_detect();
        $this->system_detect();
        $this->browser_detect();
        
        $this->Langue_Initiale = $this->language_detect();
        $this->Langue = $this->language_convert($this->Langue_Initiale);
        
        $this->referer_detect();
        $this->uri_detect();
    }
    
    /********************************************************************
     * Code créé par Minimix                                            *
     * Donné par Hédoux Julien (sg71master) pour Gatewars.eu            *
     * Adapté en POO et mit à jour pour la langue par Vermeulen Maxime  *
     ********************************************************************/
    
    /**
     * Trouve l'ip réelle si un proxy est detecté
     * 
     * @return string L'ip réel de l'user
     */
    private function proxy_detect()
    {
        $array = array(
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_VIA',
            'HTTP_X_COMING_FROM',
            'HTTP_COMING_FROM',
            'HTTP_CLIENT_IP'
        );
        
        $ret = false;
        foreach($array as $key)
        {
            if(isset($_SERVER[$key]) && !empty($_SERVER[$key]))
            {
                if($ret == false)
                {
                    $this->Proxy = $_SERVER[$key];
                }
                $ret = true;
            }
        }
        
        if($ret == false)
        {
            $this->Proxy = NULL;
        }
        
        return $this->Proxy;
    }
    
    /**
     * Rempli l'attribut $this->Proxy_ip avec l'ip du proxy, false sinon
     */
    private function proxy_ip_detect()
    {
        if($this->Proxy != NULL)
        {
            $this->Proxy_ip = $_SERVER['REMOTE_ADDR'];
        }
        else
        {
            $this->Proxy_ip = FALSE;
        }
    }
    
    /**
     * Rempli l'attribut $this->Proxy_host avec l'host du proxy, false sinon
     */
    private function proxy_host_detect()
    {
        //Commenté car gethostbyaddr a des tendences aux lags.
        /*
        if($this->Proxy != NULL) {$this->Proxy_host = @gethostbyaddr($_SERVER['REMOTE_ADDR']);}
        else {$this->Proxy_host = FALSE;}
        */
        $this->Proxy_host = FALSE;
    }
    
    /**
     * Rempli l'attribut $this->Ip avec l'ip du client (ip réel si derrière un proxy)
     */
    private function real_ip_detect()
    {
        if($this->Proxy != NULL)
        {
            $this->Ip = $this->Proxy;
        }
        else
        {
            $this->Ip = $_SERVER['REMOTE_ADDR'];
        }
    }
    
    /**
     * Rempli l'attribut $this->Host avec l'host du client (l'host réel si derrière un proxy)
     */
    private function real_host_detect()
    {
        //Commenté car gethostbyaddr a des tendences aux lags.
        /*
        if($this->Proxy != NULL) {$this->Host = @gethostbyaddr($this->Proxy);}
        else {$this->Host = @gethostbyaddr($_SERVER['REMOTE_ADDR']);}
        */
        $this->Host = '';
    }
    
    /**
     * Détecte l'os de l'user et le met dans l'attribut $this->OS
     */
    private function system_detect()
    {
        $array = array
        (
            '(win|windows) ?(9x ?4\.90|Me)'     => 'Windows ME',
            '(win|windows) ?(95)'               => 'Windows 95',
            '(win|windows) ?(98)'               => 'Windows 98',
            '(win|windows) ?(2000)'             => 'Windows 2000',
            '(Windows NT 5.0)'                  => 'Windows NT',
            '(Windows NT 5.1)'                  => 'Windows XP',
            '(win|windows) ?XP'                 => 'Windows XP',
            '(win|windows) ?XP ?(LSD)'          => 'Windows LSD',
            '(Windows NT 6.0)'                  => 'Windows Vista',
            '(Windows NT 6.1)'                  => 'Windows 7',
            '(Windows NT 6.2)'                  => 'Windows 8',
            '(win|windows)'                     => 'Windows',
            '(Android 1.5)'                     => 'Android 1.5',
            '(Android 2.0)'                     => 'Android 2.0',
            '(Android 3.0)'                     => 'Android 3.0',
            '(Android 4.0)'                     => 'Android 4.0',
            '(Android)'                         => 'Android',
            '(linux)'                           => 'Linux',
            '(J2ME/MIDP)'                       => 'Mobile',
            'SunOs'                             => 'SunOs',
            'Wii'                               => 'Wii',
            '(freebsd)'                         => 'FreeBSD',
            '(openbsd)'                         => 'OpenBS',
            '(netbsd)'                          => 'NetBSD',
            '(AIX)'                             => 'AIX',
            '(QNX)'                             => 'QNX',
            '(HP-UX)'                           => 'HP-UX',
            '(IRIX)'                            => 'IRIX',
            '(unix|x11)'                        => 'UNIX',
            '(Macintosh|PPC)'                   => 'Macintosh',
            '(mac|ppc)'                         => 'Macintosh',
            'beos'                              => 'BeOS',
            'os/2'                              => 'OS/2'
        );
        
        $ret = false;
        foreach($array as $reg => $system)
        {
            //Mozilla/5.0 (Windows; U; Windows NT 6.0; fr; rv:1.8.1.20) Gecko/20081217 Firefox/2.0.0.20
            if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('#'.$reg.'#', $_SERVER['HTTP_USER_AGENT']))
            {
                if($ret == false)
                {
                    $this->OS = $system;
                }
                $ret = true;
            }
        }
        
        if($ret == false)
        {
            $this->OS = 'Inconnu';
        }
    }
    
    /**
     * Détecte le navigateur de l'user et le met dans l'attribut $this->Nav
     */
    private function browser_detect()
    {
        $array=array
        (
            '(MSIE)'       => 'Internet Explorer',
            '(Chrome)'     => 'Chrome',
            '(Opera)'      => 'Opera',
            '(Netscape)'   => 'Netscape',
            '(AOL)'        => 'AOL',
            '(Konqueror)'  => 'Konqueror',
            '(Lynx)'       => 'Lynx',
            '(Amaya)'      => 'Amaya',
            '(AvantGo)'    => 'AvantGo',
            '(Bluefish)'   => 'Bluefish',
            '(ICEBrowser)' => 'ICEBrowser',
            '(Safari)'     => 'Safari',
            '(Kanari)'     => 'Kanari',
            '(ICEBrowser)' => 'ICEBrowser',
            '(bot|google|slurp|scooter|spider|infoseek|arachnoidea|altavista)' => 'Search engine',
            'tv'           => 'Web TV',
            '(Firefox)'    => 'Mozilla Firefox',
            '(Mozilla)'    => 'Mozilla'
        );
        
        $ret = false;
        if(isset($_SERVER['HTTP_USER_AGENT']))
        {
            foreach($array as $reg => $browser)
            {
                if(preg_match('#'.$reg.'#', $_SERVER['HTTP_USER_AGENT']))
                {
                    if($ret == false)
                    {
                        $this->Nav = $browser;
                        if($browser == 'Search engine')
                        {
                            $this->Bot = $reg;
                        }
                    }
                    $ret = true;
                }
            }
        }
        
        if($ret == false)
        {
            $this->Nav = 'Inconnu';
        }
    }
    
    /**
     * Détecte la langue préféré de l'user via l'UserAgent
     * 
     * @return string La langue préféré de l'user au format xx-yy (exemple : fr-fr ou en-us)
     */
    private function language_detect()
    {
        /*
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] -> fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4
        D'abord fr-FR (préférence 1/1)
        Puis dans l'ordre, fr (préférence 0.8 / 1)
        Puis en-US (préférence 0.6/1)
        Puis en (préférence 0.4/1)
        */
        
        $language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $ex = explode(',', $language);
        
        $ex2 = explode(';', $ex[0]);
        $lang_user = strtolower($ex2[0]);
        $lang = $lang_user;
        
        if(strpos($lang, '-') !== false)
        {
            $ex3 = explode('-', $lang);
            $lang = $ex3[0];
        }
        
        return $lang;
    }
    
    /**
     * Retourne la langue choisie pour l'user au format humain
     * 
     * @param string $lang Les initiale de la langue choisie
     * 
     * @return string La langue choisie. "Inconnue" si elle n'a pas été trouvée.
     */
    private function language_convert($lang='')
    {
        $array = array(
            'AF'   => 'Afrikaans',
            'SQ'   => 'Albanais',
            'DE'   => 'Allemand',
            'EN'   => 'Anglais',
            'AR'   => 'Arabe',
            'AN'   => 'Aragonais',
            'HY'   => 'Arménien',
            'AS'   => 'Assamais',
            'AST'  => 'Asturien',
            'AZ'   => 'Azéri',
            'EU'   => 'Basque',
            'BN'   => 'Bengali',
            'BE'   => 'Biélorusse',
            'BS'   => 'Bosniaque',
            'PTBR' => 'Brézil',
            'BR'   => 'Brézil',
            'BG'   => 'Bulgare',
            'KM'   => 'Cambodgien',
            'CA'   => 'Catalan',
            'CH'   => 'Chamaroo',
            'ZH'   => 'Chinois',
            'KO'   => 'Coréen',
            'CO'   => 'Corse',
            'HR'   => 'Croate',
            'DA'   => 'Danois',
            'ES'   => 'Espagnol',
            'EO'   => 'Espéranto',
            'ET'   => 'Estonien',
            'FJ'   => 'Fidjien',
            'FR'   => 'Français',
            'HT'   => 'Haïtien',
            'HE'   => 'Hébreu',
            'HU'   => 'Hongrois',
            'HI'   => 'Hindi',
            'ID'   => 'Indonésien',
            'GA'   => 'Irlandais',
            'IS'   => 'Islandais',
            'IT'   => 'Italien',
            'JA'   => 'Japonais',
            'LA'   => 'Latin',
            'LT'   => 'Lituanien',
            'MK'   => 'Macédoine',
            'MS'   => 'Malais',
            'MO'   => 'Moldave',
            'NL'   => 'Néerlandais',
            'NE'   => 'Népalais',
            'NO'   => 'Norvégiens',
            'PA'   => 'Penjabi',
            'FA'   => 'Persan',
            'PL'   => 'Polonais',
            'PT'   => 'Portuguais',
            'RO'   => 'Roumain',
            'RU'   => 'Russe',
            'SRCYR'=> 'Serbe',
            'SR'   => 'Serbe',
            'SK'   => 'Slovaque',
            'SL'   => 'Slovène',
            'SV'   => 'Suédois',
            'CS'   => 'Tchèque',
            'CE'   => 'Tchétchène',
            'TH'   => 'Thaï',
            'TR'   => 'Turque',
            'UK'   => 'Ukrainien',
            'VI'   => 'Vietnamien',
            'YI'   => 'Yiddish',
        );
        
        $lang = strtoupper($lang);
        if(array_key_exists($lang, $array))
        {
            return $array[$lang];
        }
        else
        {
            return 'Inconnue';
        }
    }
    
    /**
     * Indique dans l'attribut $this->Proviens l'url d'où viens l'user. "Inconnu" si elle n'a pas été trouvée.
     */
    private function referer_detect()
    {
        if(!empty($_SERVER['HTTP_REFERER']))
        {
            $this->Proviens = $_SERVER['HTTP_REFERER'];
        }
        else
        {
            $this->Proviens = 'Inconnu';
        }
    }
    
    /**
     * Indique dans l'attribut $this->Url l'url sur laquel se trouve l'user. "Inconnu" si on trouve pas.
     */
    private function uri_detect()
    {
        if(!empty($_SERVER['REQUEST_URI']))
        {
            $this->Url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }
        else
        {
            $this->Url = 'Inconnu';
        }
    }
}
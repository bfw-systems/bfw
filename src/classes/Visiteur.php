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
    protected $_kernel;
    
    /**
     * @var $idSession Id de la session correspondante
     */
    protected $idSession = null;
    
    /**
     * @var $ip Son ip
     */
    protected $ip = '';
    
    /**
     * @var $host L'hostname du visiteur
     */
    protected $host = '';
    
    /**
     * @var $proxy S'il passe par un proxy
     */
    protected $proxy = '';
    
    /**
     * @var $proxyIp L'ip du proxy
     */
    protected $proxyIp = '';
    
    /**
     * @var $proxyHost L'hostname du proxy
     */
    protected $proxyHost = '';
    
    /**
     * @var $os Son système d'exploitation
     */
    protected $os = '';
    
    /**
     * @var $nav Son navigateur
     */
    protected $nav = '';
    
    /**
     * @var $langue La langue de l'utilisateur
     */
    protected $langue = '';
    
    /**
     * @var $langueInitiale Les initiale de la langue
     */
    protected $langueInitiale = '';
    
    /**
     * @var $proviens L'url d'où il vient
     */
    protected $proviens = '';
    
    /**
     * @var $url Son url actuelle
     */
    protected $url = '';
    
    /**
     * @var $bot S'il s'agit d'un robot
     */
    protected $bot = '';
    
    /**
     * Accesseur vers l'attribut $idSession
     */
    public function getIdSession()
    {
        return $this->idSession;
    }
    
    /**
     * Accesseur vers l'attribut $ip
     */
    public function getIp()
    {
        return $this->ip;
    }
    
    /**
     * Accesseur vers l'attribut $host
     */
    public function getHost()
    {
        return $this->host;
    }
    
    /**
     * Accesseur vers l'attribut $proxy
     */
    public function getProxy()
    {
        return $this->proxy;
    }
    
    /**
     * Accesseur vers l'attribut $proxyIp
     */
    public function getProxyIp()
    {
        return $this->proxyIp;
    }
    
    /**
     * Accesseur vers l'attribut $proxyHost
     */
    public function getProxyHost()
    {
        return $this->proxyHost;
    }
    
    /**
     * Accesseur vers l'attribut $os
     */
    public function getOs()
    {
        return $this->os;
    }
    
    /**
     * Accesseur vers l'attribut $nav
     */
    public function getNav()
    {
        return $this->nav;
    }
    
    /**
     * Accesseur vers l'attribut $langue
     */
    public function getLangue()
    {
        return $this->langue;
    }
    
    /**
     * Accesseur vers l'attribut $langueInitiale
     */
    public function getLangueInitiale()
    {
        return $this->langueInitiale;
    }
    
    /**
     * Accesseur vers l'attribut $proviens
     */
    public function getProviens()
    {
        return $this->proviens;
    }
    
    /**
     * Accesseur vers l'attribut $url
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * Accesseur vers l'attribut $bot
     */
    public function getBot()
    {
        return $this->bot;
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
            $this->idSession = $_SESSION['idSess'];
        }
    }
    
    /**
     * Récupère les différentes infos sur le visiteur
     */
    protected function recup_infos()
    {
        $this->proxyDetect();
        $this->proxyIpDetect();
        $this->proxyHostDetect();
        $this->realIpDetect();
        $this->realHostDetect();
        //$this->port_detect();
        $this->systemDetect();
        $this->browserDetect();
        
        $this->langueInitiale = $this->languageDetect();
        $this->langue = $this->languageConvert($this->langueInitiale);
        
        $this->refererDetect();
        $this->uriDetect();
    }
    
    /********************************************************************
     * Code créé par Minimix                                            *
     * Donné par Hédoux Julien (sg71master) pour Gatewars.eu            *
     * Adapté en POO et mit à jour pour la langue par Vermeulen Maxime  *
     ********************************************************************/
    
    /**
     * Trouve l'ip réelle si un proxy est detecté
     * 
     * @return string|null L'ip réel de l'user
     */
    protected function proxyDetect()
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
        
        foreach($array as $key)
        {
            if(isset($_SERVER[$key]) && !empty($_SERVER[$key]))
            {
                $this->proxy = $_SERVER[$key];
                return $this->proxy;
            }
        }
        
        $this->proxy = null;
        return $this->proxy;
    }
    
    /**
     * Rempli l'attribut $this->proxyIp avec l'ip du proxy, false sinon
     */
    protected function proxyIpDetect()
    {
        $this->proxyIp = '';
        
        if($this->proxy != NULL)
        {
            $this->proxyIp = $_SERVER['REMOTE_ADDR'];
        }
    }
    
    /**
     * Rempli l'attribut $this->proxyHost avec l'host du proxy, false sinon
     */
    protected function proxyHostDetect()
    {
        //Commenté car gethostbyaddr a des tendences aux lags.
        /*
        if($this->proxy != NULL) {$this->proxyHost = @gethostbyaddr($_SERVER['REMOTE_ADDR']);}
        else {$this->proxyHost = FALSE;}
        */
        $this->proxyHost = '';
    }
    
    /**
     * Rempli l'attribut $this->ip avec l'ip du client (ip réel si derrière un proxy)
     */
    protected function realIpDetect()
    {
        $this->ip = 'Unknown';
        
        if($this->proxy != NULL)
        {
            $this->ip = $this->proxy;
        }
        elseif(isset($_SERVER['REMOTE_ADDR']))
        {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }
    }
    
    /**
     * Rempli l'attribut $this->host avec l'host du client (l'host réel si derrière un proxy)
     */
    protected function realHostDetect()
    {
        //Commenté car gethostbyaddr a des tendences aux lags.
        /*
        if($this->proxy != NULL) {$this->host = @gethostbyaddr($this->proxy);}
        else {$this->host = @gethostbyaddr($_SERVER['REMOTE_ADDR']);}
        */
        $this->host = '';
    }
    
    /**
     * Détecte l'os de l'user et le met dans l'attribut $this->OS
     */
    protected function systemDetect()
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
        
        foreach($array as $reg => $system)
        {
            //Mozilla/5.0 (Windows; U; Windows NT 6.0; fr; rv:1.8.1.20) Gecko/20081217 Firefox/2.0.0.20
            if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('#'.$reg.'#i', $_SERVER['HTTP_USER_AGENT']))
            {
                $this->os = $system;
                return;
            }
        }
        
        $this->os = 'Inconnu';
    }
    
    /**
     * Détecte le navigateur de l'user et le met dans l'attribut $this->nav
     */
    protected function browserDetect()
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
        
        if(isset($_SERVER['HTTP_USER_AGENT']))
        {
            foreach($array as $reg => $browser)
            {
                $match = array();
                if(preg_match('#'.$reg.'#i', $_SERVER['HTTP_USER_AGENT'], $match))
                {
                    $this->nav = $browser;
                    if($browser == 'Search engine')
                    {
                        $this->bot = $match[1];
                    }
                    
                    return;
                }
            }
        }
        
        $this->nav = 'Inconnu';
    }
    
    /**
     * Détecte la langue préféré de l'user via l'UserAgent
     * 
     * @return string La langue préféré de l'user au format xx-yy (exemple : fr-fr ou en-us)
     */
    protected function languageDetect()
    {
        /*
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] -> fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4
        D'abord fr-FR (préférence 1/1)
        Puis dans l'ordre, fr (préférence 0.8 / 1)
        Puis en-US (préférence 0.6/1)
        Puis en (préférence 0.4/1)
        */
        
        $lang = 'Unknown';
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        {
            $language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $ex       = explode(',', $language);
            
            $ex2  = explode(';', $ex[0]);
            $lang = strtolower($ex2[0]);
            
            if(strpos($lang, '-') !== false)
            {
                $ex3  = explode('-', $lang);
                $lang = $ex3[0];
            }
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
    protected function languageConvert($lang='')
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
        
        return 'Inconnue';
    }
    
    /**
     * Indique dans l'attribut $this->proviens l'url d'où viens l'user. "Inconnu" si elle n'a pas été trouvée.
     */
    protected function refererDetect()
    {
        $this->proviens = 'Inconnu';
        if(!empty($_SERVER['HTTP_REFERER']))
        {
            $this->proviens = $_SERVER['HTTP_REFERER'];
        }
    }
    
    /**
     * Indique dans l'attribut $this->url l'url sur laquel se trouve l'user. "Inconnu" si on trouve pas.
     */
    protected function uriDetect()
    {
        $this->url = 'Inconnu';
        if(!empty($_SERVER['REQUEST_URI']))
        {
            $this->url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }
    }
}

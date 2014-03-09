<?php
/**
 * Classes en rapport avec la gestion du Cache
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFW;

/**
 * Classe gérant le cache
 * @package bfw
 */
class Cache implements \BFWInterface\ICache
{
    /**
     * @var $_kernel L'instance du Kernel
     */
    private $_kernel;
    
    /**
     * @var $controler Le controleur qu'on doit lire
     */
    private $controler;
    
    //Partie 1
    /**
     * @var $codeOneLine Tout le code du fichier controleur sur 1 seule ligne.
     */
    private $codeOneLine = '';
    
    //Partie 2
    /**
     * @var $noTpl Si à true, la classe Template n'est pas appelé !
     */
    private $noTpl = false;
    
    /**
     * @var $VarTpl Le nom de la variable attribué à l'instance Template
     */
    private $VarTpl = '';
    
    /**
     * @var $linkTpl Le "lien" vers le fichier de vue
     */
    private $linkTpl = '';
    
    //Partie 3
    /**
     * @var $lstBlockVue La liste des block présent dans le fichier de vue.
     */
    private $lstBlockVue;
    
    /**
     * Constructeur
     * 
     * @param string $controler Le controleur qu'on doit lire
     */
    public function __construct($controler)
    {
        $this->_kernel = getKernel();
        
        $this->controler = $controler;
    }
    
    /**
     * Lance la création du cache
     */
    public function run()
    {
        /*
         * Récupère la liste des vues utilisé (le lien vers la vue et le n° de ligne)
         * Permet aussi de savoir si des erreurs seront présente
         */ 
        if($this->_kernel->getDebug()) {echo 'Partie 1 : Mise sur une ligne<br/>';}
        $this->read(); //Récupération de la liste des vues.
        
        if($this->_kernel->getDebug()) {echo 'Partie 2 : Recherche de la vue<br/>';}
        $this->rechercheTpl();
        if($this->noTpl == true)
        {
            if($this->_kernel->getDebug()) {echo '&nbsp;&nbsp;Pas de fichier de vue.<br/>Mise en cache directement.';}
            $this->copyDirect2Cache();
        }
        else
        {
            if($this->_kernel->getDebug()) {echo '&nbsp;&nbsp;Fichier de vue : '.$this->linkTpl.'<br/><br/>';}
            
            /**
             * Liste de tous les blocks de la vue
             */
            if($this->_kernel->getDebug()) {echo 'Partie 3 : Récupération des la liste des blocks de la vue.<br/>';}
            $this->lstBlockView();
            if($this->_kernel->getDebug()) {echo '&nbsp;&nbsp;Done.<br/><br/>';}
            
            /**
             * Final !
             * Saut de ligne dans le code pour le cache et remplacement au fur et à mesure.
             */
            if($this->_kernel->getDebug()) {echo 'Partie 4 : Grand final !<br/>';}
            $newCode = $this->createEOLEtAddVue();
            if($this->_kernel->getDebug()) {echo '&nbsp;&nbsp;Done.<br/><br/>';}
            
            $code = implode("\n", $newCode);
            $this->WriteCache($code);
        }
    }
    
    /**
     * Accesseur vers l'attribut $controler
     * 
     * @param string $controler Le controleur qu'on doit lire
     */
    public function set_controler($controler)
    {
        $this->controler = $controler;
    }
    
    /**
     * Si une erreur est trouvé. On l'affiche et on arrête le script.
     * 
     * @param string $txt L'erreur à afficher.
     */
    protected function error($txt)
    {
        echo $txt;
        exit;
    }
    
    /**
     * Partie 1 : Lecture du fichier php et mise sur 1 ligne dans une var.
     * Les commentaires sont supprimé dans le code en 1 ligne.
     */
    private function read()
    {
        //Ouverture du fichier controleur
        $fopen = fopen(path_controler.$this->controler.'.php', 'r');
        
        while($line = fgets($fopen)) //Lecture de la ligne du fichier
        {
            $line = trim($line);
            
            //Gestion si la ligne est un commentaire sur 1 ligne.
            $posComm = strpos($line, '//');
            if($posComm !== false)
            {
                $line = substr($line, 0, $posComm);
            }
            
            $this->codeOneLine .= $line; //Met la ligne dans l'attribue $this->codeOneLine.
        }
        
        fclose($fopen); //Fermeture du fichier controleur
        
        //Maintenant il faut virer les commentaire du type /* */
        $end = false;
        $code = $this->codeOneLine;
        
        do
        {
            $posDeb = strpos($code, '/*');
            
            if($posDeb !== false)
            {
                $posFin = strpos($code, '*/');
                if($posFin === false)
                {
                    $this->error('Erreur php trouvée : Commentaire multi-ligne non fermé !');
                }
                $posFin += 2; //Pour se positionner après le / de */
                
                $partBefore = substr($code, 0, $posDeb);
                $partAfter = substr($code, $posFin);
                
                $code = $partBefore.$partAfter;
            }
            else
            {
                $end = true;
            }
        }
        while($end == false);
        
        $this->codeOneLine = $code;
    }
    
    /**
     * Partie 2 : Recherche de la vue.
     */
    private function rechercheTpl()
    {
        $nbFind = substr_count($this->codeOneLine, 'new Template');
        
        if($nbFind == 0)
        {
            $this->noTpl = true;
        }
        elseif($nbFind > 1)
        {
            $this->error('Le système de cache actuel ne gère pas plusieurs instance de Template pour l\'instant.');
        }
        else
        {
            $posTemplate = strpos($this->codeOneLine, 'new Template');
            $lenght = strlen($this->codeOneLine);
            $posDepuisFin = $posTemplate - $lenght; //Valeur négative voulu.
            
            $findVar = strrpos($this->codeOneLine, '$', $posDepuisFin);
            $finCreateTpl = strpos($this->codeOneLine, ';', $posTemplate);
            
            $createTpl = substr($this->codeOneLine, $findVar, ($finCreateTpl - $findVar));
            
            $info = array();
            preg_match('#\$(.*)=([\s]*)new Template\((.*)\)#i', $createTpl, $info);
            $exInfo = explode(',', $info[3]);
            $info[3] = $exInfo[0];
            
            //Vérification que la méthode End() est bien appelé.
            $this->VarTpl = $nameVar = trim($info[1]);
            if(strpos($this->codeOneLine, '$'.$nameVar.'->End();') === false)
            {
                $this->error('Pas d\'appel à la méthode End() du template trouvé.');
            }
            
            //On cherche s'il y a quelque chose avant le texte de la vue
            $search = preg_match('#(\$|(\.\'))#i', $info[3]);
            if($search === false) //Que du texte.
            {
                $vue = $info[3];
            }
            else
            {
                //Une variable ou une constante à été utilisé
                
                //C'est une variable.
                if(strpos($info[3], '$') !== false)
                {
                    //On part du principe que c'est obligatoirement ainsi : $xxx.'blabla'
                    //On se le permet car de toute façon on ne trouve que les variables déclaré dans le kernel.
                    //Et ce sont les paths.
                    //Les autres variables seront de toute façon pas trouvé.
                    
                    $searchtplvar = array();
                    $searchtpl = preg_match('#\$(.*)#i', $info[3], $searchtplvar);
                    $namevar = substr($searchtplvar[1], 0, strpos($searchtplvar[1], '.'));
                    
                    if(!isset($GLOBALS[$namevar]))
                    {
                        $this->error('Variable $'.$namevar.' non accessible pour le système de cache.');
                    }
                    else
                    {
                        $vue = $GLOBALS[$namevar].substr($searchtplvar[1], (strpos($searchtplvar[1], '.')+2), -1);
                    }
                }
                else
                {
                    //C'est une constante
                    
                    //On récupère les infos sur la constante
                    $infosCst = array();
                    $searchCst = preg_match('#(.*)(\.\'(.*))#i', $info[3], $infosCst);
                    
                    $vue = '';
                    $cst = $infosCst[1];
                    $finlink = $infosCst[3];
                    
                    //On regarde si la constante existe.
                    //(Donc seule les constantes du framework peuvent être utilisé).
                    if(defined($cst))
                    {
                        $vue = constant($cst);
                    }
                    else
                    {
                        $this->error('La constante '.$cst.' n\'a pas été trouvé par le système de cache.');
                    }
                    
                    $vue .= substr($finlink, 0, -1);
                }
            }
            
            $this->linkTpl = $vue;
            
            if(!file_exists($this->linkTpl))
            {
                $this->error('Le fichier de vue n\'a pu être trouvé.');
            }
        }
    }
    
    /**
     * Partie 3
     * Liste les block présent dans la vue
     */
    private function lstBlockView()
    {
        $fop = fopen($this->linkTpl, 'r'); //Ouverture du fichier de vue
        
        while($line = fgets($fop))
        {
            if(strpos($line, '<block') !== false) //S'il y a la balise "block" dans la ligne
            {
                //On récupère les infos sur la balise block
                $search = array();
                preg_match('#<block name="([0-9a-zA-Z._-]+)">#i', $line, $search);
                
                $this->lstBlockVue[] = $search[1]; //Et on stock le nom.
            }
        }
        
        fclose($fop);
    }
    
    /**
     * Partie 4
     * Recréer les EOL dans le code en 1 ligne (épuré des commentaires).
     * Remplace les méthodes de la classe Template par l'équivalent du cache.
     */
    private function createEOLEtAddVue()
    {
        $code = $this->codeOneLine; //Le code en 1 ligne
        $newCode = array(); //Le code qui sera généré mis sous la forme d'un array.
        $end = false; //Utile pour sortir du do...while
        
        //La liste des blocks (if etc) dans le controleur
        $openBlock = array(); //Liste des blocks
        $keyOpenBlock = 0; //Permet de savoir la clé du dernier élément du tableau $openBlock
        
        //La liste des blocks (if etc) dans la vue
        $openBlockView = array(); //Liste des blocks
        $keyOpenBlockView = 0; //Permet de savoir la clé du dernier élément du tableau $openBlockView
        
        $code2add = array(); //Le code qui sera à ajouter à la fin d'un block du controleur.
        
        do
        {
            /**
             * $lstToFind : 
             * * key 0 : <?php
             * * key 1 : ;
             * * key 2 : {
             * * key 3 : :
             * * key 4 : }
             * * key 5 : endif
             * * key 6 : endwhile
             * * key 7 : endfor
             * * key 8 : endforeach
             * * key 9 : endswitch
             */
            $lstToFind = array('<?php', ';', '{', ':', '}', 'endif', 'endwhile', 'endfor', 'endforeach', 'endswitch');
            $strpos = array(); //On y stock le résultat d'strpos de chaque clé de $lstToFind
            foreach($lstToFind as $search)
            {
                $strpos[] = strpos($code, $search);
            }
            asort($strpos); //On tri le tableau par ordre croissant suivant les clé (les false sont au début)
            
            //On cherche la valeur la plus petite
            $keyFind = null;
            $posFind = null;
            foreach($strpos as $key => $val)
            {
                if($val !== false) //Si ce n'est pas un false
                {
                    //Alors on a trouvé une valeur, on la stock...
                    $keyFind = $key;
                    $posFind = $val;
                    break; //...et on sort de la boucle car on s'en fou du reste (1 à a la fois)
                }
            }
            
            if($keyFind === null) //Si plus rien n'a été trouvé. Stop do...while();
            {
                $newCode[] = $code; //Il faut penser à mettre le code qui reste dans l'array ^^
                $end = true;
            }
            else //Traitement de ce qui a été trouvé.
            {
                //Si $keyFind > 3 il faut faire un EOL avant et après. Sinon seulement après.
                if($keyFind < 4)
                {
                    //Découpe du bout de code concerné
                    $code2analise = substr($code, 0, ($posFind + strlen($lstToFind[$keyFind])));
                    $ToSave = '';
                }
                else //Il s'agit de balise fermente de block.
                {
                    //On récupère les bouts de codes de la ligne
                    $code2analise = substr($code, 0, $posFind);
                    $ToSave = substr($code, $posFind, strlen($lstToFind[$keyFind]));
                    
                    //On regarde s'il y a du code de vue à ajouter avant de fermer le block
                    if(isset($code2add[$keyOpenBlock]))
                    {
                        //Si oui on liste chaque ligne à ajouter
                        foreach($code2add[$keyOpenBlock] as $val)
                        {
                            //Et on les ajoute au code généré
                            $newCode[] = '$BFWCacheBlock[\''.$openBlock[$keyOpenBlock]['name'].'\'] .= '.$val;
                        }
                        
                        //On supprime le code qui viens d'être ajouté au tableau qui les transmettait.
                        unset($code2add[$keyOpenBlock]);
                    }
                    
                    //On enlève à l'array le block qu'on viens de voir fermé.
                    array_pop($openBlock);
                    $keyOpenBlock--;
                }
                    
                //Ajout à l'array des codes.
                if($code2analise != '')
                {
                    $otherCode = false; //Permet de savoir si on ajoute le code d'origine ou pas (victime de modification).
                    
                    //On veux savoir si on est en début de block ou pas.
                    $search = preg_match('#((if|else|elseif|while|for|foreach)([\s]*)\()|(do)#i', $code2analise);
                    if($search == true) //On est en début de block
                    {
                        //On ajoute le block dans la liste des block ouvert
                        $keyOpenBlock = array_push($openBlock, array()); //(retourne le nombre d'élément dans le tableau)
                        $keyOpenBlock--; //Et maj de la clé du block
                    }
                    
                    //On veux savoir si on est sur une instanciation de Template
                    $searchInfo = array();
                    $search = preg_match('#new Template\((.*)\)#i', $code2analise, $searchInfo);
                    if($search == true) //La template est instancié
                    {
                        $exInfo = explode(',', $searchInfo[1]); //On récupère le texte en paramètre
                        
                        //Recherche si des vars sont envoyé à la vue.
                        $vars = '';
                        if(count($exInfo) > 1)
                        {
                            unset($exInfo[0]);
                            $vars = implode(',', $exInfo);
                        }
                        
                        //Maintenant on remplace.
                        $newCode[] = '$BFWCacheVars = $BFWCacheBlock = $BFWCacheBlockVars = array();';
                        
                        $addcode = '$BFWCacheHTML = ';
                        foreach($this->lstBlockVue as $val)
                        {
                            $addcode .= '$BFWCacheBlock[\''.$val.'\'] = ';
                        }
                        
                        $addcode .= '\'\';';
                        $newCode[] = $addcode;
                        
                        $addcode = '';
                        foreach($this->lstBlockVue as $val)
                        {
                            $addcode .= '$BFWCacheBlockVars[\''.$val.'\'] = ';
                        }
                        
                        $addcode .= 'array();';
                        $newCode[] = $addcode;
                        
                        $otherCode = true;
                        if($vars != '')
                        {
                            $newCode[] = 'BFWCacheAddVars(\'html\', '.$vars.');';
                        }
                        
                    }
                    
                    //Recherche s'il y a un appel à la méthode AddGeneralVars()
                    $searchInfo = array();
                    $search = preg_match('#\$'.$this->VarTpl.'->AddGeneralVars\((.*)\);#i', $code2analise, $searchInfo);
                    if($search == true) //C'est le cas
                    {
                        $otherCode = true;
                        //On récupère le contenu du paramètre de AddGeneralVars et on met en paramètre de la fonction cache.
                        $newCode[] = 'BFWCacheAddVars(\'all\', '.$searchInfo[1].');';
                    }
                    
                    //Recherche s'il y a un appel à la méthode AddVars
                    $searchInfo = array();
                    $search = preg_match('#\$'.$this->VarTpl.'->AddVars\((.*)\);#i', $code2analise, $searchInfo);
                    if($search == true)
                    {
                        //On regarde si on est dans un block de controleur ou pas.
                        //Si c'est le cas on passe en paramètre de la fct de cache le nom du block
                        if(count($openBlockView) > 0)
                        {
                            $nomBlock = $openBlockView[$keyOpenBlockView]['nom'];
                        }
                        else //Sinon on met 'html' en paramètre de la fct de cache
                        {
                            $nomBlock = 'html';
                        }
                        
                        $otherCode = true;
                        $newCode[] = 'BFWCacheAddVars(\''.$nomBlock.'\', '.$searchInfo[1].');';
                    }
                    
                    //On recherche si on appelle méthode AddBlockWithEnd
                    $searchInfo = array();
                    $search = preg_match('#\$'.$this->VarTpl.'->AddBlockWithEnd\((.*)\);#i', $code2analise, $searchInfo);
                    if($search == true)
                    {
                        //On récupère les infos du paramètre
                        $exInfo = explode(',', $searchInfo[1]);
                        $nomBlock = $exInfo[0];
                        $vars = '';
                        
                        //On vérifie si des variables sont envoyé à la vue pour le block
                        if(count($exInfo) > 1)
                        {
                            unset($exInfo[0]); //Si oui on enlève la 1ere partie du paramètre (nom du block)
                            $vars = implode(',', $exInfo); //On remet en 1 ligne en remettant les ","
                            
                            //Et on ajoute l'appel à la fct du cache dans le code généré
                            $newCode[] = 'BFWCacheAddVars('.$nomBlock.', '.$vars.');';
                        }
                        
                        //On enlève les guillemet autour du nom du block s'il y en a.
                        if($nomBlock{0} == '\'' || $nomBlock{0} == '"')
                        {
                            $nomBlock = substr($nomBlock, 1, -1);
                        }
                        
                        //Ajout du code html.
                        $html = $this->recupHtml($nomBlock); //On récupère le code html
                        foreach($html as $val) //Et on l'ajoute directement au code généré
                        {
                            $newCode[] = $val;
                        }
                        
                        $otherCode = true;
                    }
                    
                    //On regarde s'il y a un appelle à la méthode AddBlock()
                    $searchInfo = array();
                    $search = preg_match('#\$'.$this->VarTpl.'->AddBlock\((.*)\);#i', $code2analise, $searchInfo);
                    if($search == true)
                    {
                        //On récupère les infos du paramètre
                        $exInfo = explode(',', $searchInfo[1]);
                        $vars = '';
                        $nomBlock = $exInfo[0];
                        
                        //On vérifie si des variables sont envoyé à la vue pour le block
                        if(count($exInfo) > 1)
                        {
                            unset($exInfo[0]); //Si oui on enlève la 1ere partie du paramètre (nom du block)
                            $vars = implode(',', $exInfo); //On remet en 1 ligne en remettant les ","
                            
                            //Et on ajoute l'appel à la fct du cache dans le code généré
                            $newCode[] = 'BFWCacheAddVars('.$nomBlock.', '.$vars.');';
                        }
                        
                        //On enlève les guillemet autour du nom du block s'il y en a.
                        if($nomBlock{0} == '\'' || $nomBlock{0} == '"')
                        {
                            $nomBlock = substr($nomBlock, 1, -1);
                        }
                        
                        //On ajoute le nom (dans la vue) du block dans la liste des block (du controleur) ouvert
                        $openBlock[$keyOpenBlock]['name'] = $nomBlock;
                        $html = $this->recupHtml($nomBlock); //On récupère le code html
                        
                        //On ajoute le code html récupéré dans l'array $code2add afin de l'ajouter à la fin du block controleur.
                        foreach($html as $val)
                        {
                            $code2add[$keyOpenBlock][] = $val;
                        }
                        $otherCode = true;
                    }
                    
                    //On regarde s'il y a un appelle à la méthode End()
                    $searchInfo = array();
                    $search = preg_match('#\$'.$this->VarTpl.'->End\((.*)\);#i', $code2analise, $searchInfo);
                    if($search == true)
                    {
                        //On affiche tout le code de la vue.
                        
                        $html = $this->recupHtml(); //On récupère le code qui est en dehors des blocks
                        foreach($html as $val) //On l'ajoute au code généré
                        {
                            $newCode[] = '$BFWCacheHTML .= '.$val;
                        }
                        
                        $newCode[] = 'echo $BFWCacheHTML;'; //On ajoute un echo de la variable
                        $otherCode = true;
                    }
                    
                    //On ne fait rien si EndBlock ou remonte. Juste pas de code ajouté au code généré.
                    $search = preg_match('#\$'.$this->VarTpl.'->EndBlock\((.*)\);#i', $code2analise);
                    if($search == true)
                    {
                        $otherCode = true;
                    }
                    
                    $search = preg_match('#\$'.$this->VarTpl.'->remonte\((.*)\);#i', $code2analise);
                    if($search == true)
                    {
                        $otherCode = true;
                    }
                    
                    //Si on garde le code d'origine, on l'ajoute au code généré
                    if($otherCode == false)
                    {
                        $newCode[] = $code2analise;
                    }
                }
                
                if($ToSave != '') //Ajout des dernières lignes du découpage de la ligne.
                {
                    $newCode[] = $ToSave;
                }
                
                //Et découper le code qui reste à analyser (on enlève la partie prise)
                $code = substr($code, ($posFind + strlen($lstToFind[$keyFind])));
            }
        }
        while($end == false);
        
        return $newCode;
    }

    /**
     * Récupère le code html qui est entre le block indiqué en paramètre.
     * 
     * @param string|null $nomBlock (default: null) Le nom du block dont on doit retourner le contenu. A null pour en dehors des blocks.
     */
    private function recupHtml($nomBlock=null)
    {
        $fop = fopen($this->linkTpl, 'r'); //Ouverture du fichier de vue
        
        //La variable permettant de savoir s'il faut stocker le contenu lu en mémoire.
        //Par défaut à true si pas de block indiqué. Sinon il faut attendre de trouver le block.
        if($nomBlock != null)
        {
            $stock = false;
        }
        else
        {
            $stock = true;
        }
        
        $html = array(); //Le code html qui sera retourné.
        
        //Rencontre sous-block
        $ignore = false; //Savoir si on ignore cette partie du code de la vue ou non
        $nbBlock = 0; //Le nombre de block rencontré
        $nomSsBlock = ''; //Le nom du sous block rencontré (pour appel de la var).
        
        while($line = fgets($fop)) //On lit le fichier ligne par ligne
        {
            //S'il ne faut pas stocker l'info et s'il ne s'agit d'un block qui est cherché
            //(On recherche si c'est le block dont on veux le contenu).
            if($stock == false && $nomBlock != null)
            {
                //Si c'est bien notre block
                if($preg = preg_match('#(.*)<block name=("|\')'.$nomBlock.'("|\')>(.*)#i', $line, $decoupe))
                {
                    $stock = true; //On indique qu'on peut commencer à stocker
                    
                    if(strpos($line, '</block>') !== false) //Le block est sur 1 seule ligne.
                    {
                        $stock = false; //En fait nan on stockera pas la suite.
                        //On récupère juste le code entre les balises.
                        
                        $recupInfo = array();
                        preg_match('#(.*)<block name=("|\')'.$nomBlock.'("|\')>(.*)</block>(.*)#i', $line, $recupInfo);
                        $html[] = $this->remplaceBaliseVar($nomBlock, $recupInfo[4]);
                    }
                }
            }
            else //S'il faut stocker le code html
            {
                //S'il n'y a pas d'ouverture de block dedans et si la ligne doit être lu
                if(strpos($line, '<block ') === false && $ignore == false)
                {
                    if(strpos($line, '</block>') === false) //S'il n'y a pas de block fermé dans la ligne
                    {
                        //Alors on doit ajouter la ligner au code mis en mémoire
                        //Mais on doit d'abord remplacer les balises <var /> par l'appel des variables php
                        $html[] = $this->remplaceBaliseVar($nomBlock, $line);
                    }
                    else //S'il y a une balise </block> On arrête de stocker car c'est celle du block qu'on doit lire.
                    {
                        $stock = false;
                    }
                }
                elseif($ignore == true) //Si la ligne ne doit pas être mise en mémoire (sous block)
                {
                    //On vérifie s'il y a une fermeture de block
                    if(strpos($line, '</block>') !== false)
                    {
                        //On vérifie que se soit pas la fermeture d'un block qui est sur 1 ligne
                        //Si oui on décrémente le nombre de block ouvert.
                        if(strpos($line, '<block name=') === false)
                        {
                            $nbBlock--;
                        }
                    }
                    
                    if($nbBlock == 0) //Si le nombre de block ouvert atteind les 0
                    {
                        $ignore = false; //On recommence à stocker
                        //Et on ajoute au code html final un appel de la variable qui contiendra le contenu du sous-block.
                        $html[] = '$BFWCacheBlock[\''.$nomSsBlock.'\'];';
                    }
                }
                else //S'il y a une ouverture de block
                {
                    if($nbBlock == 0) //Si on est pas déjà dans un sous-block...
                    {
                        //...On récupère ses infos ...
                        $searchInfo = array();
                        $search = preg_match('<block name="([0-9a-zA-Z._-]+)">', $line, $searchInfo);
                        $nomSsBlock = $searchInfo[1]; //... Et on stock son nom pour la variable qui appelera son contenu.
                    }
                    
                    $ignore = true; //Et on indique de plus mettre en mémoire le code lu car on est dans un sous-block
                    
                    //On vérifie s'il y a pas une fermeture de block dans la même ligne
                    //Et qu'il s'agit du 1er sous-block
                    if(strpos($line, '</block>') !== false && $nbBlock == 0)
                    {
                        //Et on ajoute au code html final un appel de la variable qui contiendra le contenu du sous-block.
                        $html[] = '$BFWCacheBlock[\''.$nomSsBlock.'\'];';
                        $ignore = false; //On recommence à stocker
                    }
                    
                    $nbBlock++; //On incrémente le nombre de block ouvert
                }
            }
        }
        
        //Fermeture du fichier
        fclose($fop);
        
        //Renvoi le code html final.
        return $html;
    }

    /**
     * Remplace toutes les balises <var /> par des variables contenant leurs valeurs
     * 
     * @param string|null $nomBlock (default: null) Le nom du block dont on doit retourner le contenu. A null pour en dehors des blocks.
     * @param string      $line     La ligne qu'on lit
     * 
     * @return string La ligne avec les balises <var /> remplacé.
     */
    private function remplaceBaliseVar($nomBlock, $line)
    {
        $endRemplace = false; //Fin du do...while si mit à true
                        
        //Le contenu de la ligne qui est découpé et mis en array
        //Ceci est du au preg_match qui traite les <var /> du dernier au premier.
        //Donc on met en array, puis on inverse l'array et on obtient la ligne correct.
        //Obligé car un traitement pour ajouté des antislashes est présent et bug le preg_match si présent dedans.
        //Permet donc d'ajouter les antislashes petit à petit.
        $arrLine = array();
        
        do
        {
            //Recherche des balises <var />
            $searchlineInfo = array();
            $searchline = preg_match('#(.*)<var name=(\'|")([0-9a-zA-Z._-]+)(\'|") />(.*)#i', $line, $searchlineInfo);
            
            //Si une balise à été trouvé
            if($searchline)
            {
                $line = $searchlineInfo[1]; //Car le prochain remplacement de la balise sera sur le début de la ligne.
                $arrLine[] = str_replace('\'', '\\\'', $searchlineInfo[5]); //On ajoute les antislashes sur la fin de la ligne
                
                if($nomBlock != null) //Si on est dans la recherche du code html d'un block
                {
                    //On utilise la variable $BFWCacheBlockVars
                    $arrLine[] = '\'.$BFWCacheBlockVars[\''.$nomBlock.'\'][\''.$searchlineInfo[3].'\'].\'';
                }
                else //Si on recherche le code html en dehors de tous les block
                {
                    //Alors on utilise la variable $BFWCacheVars
                    $arrLine[] = '\'.$BFWCacheVars[\''.$searchlineInfo[3].'\'].\'';
                }
            }
            else //Plus de balise à remplacer. Fin du do...while
            {
                $endRemplace = true;
            }
        }
        while($endRemplace == false);
        
        //On ajoute le tout début de la ligne à l'array. Avec l'ajout des antislashes
        $arrLine[] = str_replace('\'', '\\\'', $line);
        krsort($arrLine); //On trie le tableau
        
        $line = '\''; //On réinitialise la variable $line contenant la ligne
        foreach($arrLine as $val) //Et on lit le tableau et ajoute les bouts de la ligne petit à petit
        {
            $line .= $val;
        }
        
        //Et on ajoute la ligne au code html qui sera à retourner.
        return rtrim($line).'\';';
    }
    
    /**
     * Pour le cas où le système de Template n'est pas utilisé dans la page
     * On copie directement le code du controleur dans le cache.
     */
    protected function copyDirect2Cache()
    {
        $this->WriteCache(file_get_contents(path_controler.$this->controler.'.php'));
    }
    
    /**
     * Ecrit le code php du cache dans le fichier de cache
     * 
     * @param string $code Le code à écrire dans la page
     */
    protected function WriteCache($code)
    {
        file_put_contents(path_cache.$this->controler.'.phtml', $code);
    }
}
?>
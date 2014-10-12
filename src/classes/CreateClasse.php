<?php
/**
 * Classes en rapport avec la génération automatique d'une autre classe
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFW;

/**
 * Classe générant une autre classe.
 * @package bfw
 */
class CreateClasse implements \BFWInterface\ICreateClasse
{
    /**
     * @var $_kernel L'instance du Kernel
     */
    private $_kernel;
    
    /**
     * @var $nom Le nom de la classe
     */
    private $nom = '';
    
    /**
     * @var $indente Le ou les caractère(s) mit pour indenté
     */
    private $indente = '    ';
    
    /**
     * @var $extends Depuis quelle classe on hérite
     */
    private $extends = '';
    
    /**
     * @var $implements Liste les interfaces de la classe
     */
    private $implements = array();
    
    /**
     * @var $ attributs Liste tous les attributs de la futur classe
     */
    private $attributs = array();
    
    /**
     * @var $attributs_porter La portée de tous les attributs (public/private/protected)
     */
    private $attributs_porter = array();
    
    /**
     * @var $attributs_option Les options passé à la méthode de création d'attribut pour chaque attribut
     */
    private $attributs_option = array();
    
    /**
     * @var $methode Liste toutes les méthodes qui sont à créer
     */
    private $methode = array();
    
    /**
     * @var $methode_porter La portée de toutes les méthodes (public/private/protected)
     */
    private $methode_porter = array();
    
    /**
     * @var $get La liste de tous les accesseur get à faire
     */
    private $get = array();
    
    /**
     * @var $set La liste de tous les accesseur set à faire
     */
    private $set = array();
    
    /**
     * @var $file Le contenu de la futur classe
     */
    private $file = '';
    
    /**
     * @var $methode_create La liste de toutes les méthodes créé (pour éviter d'en créer en double à cause des get et set)
     */
    private $methode_create = array();
    
    
    /**
     * Constructeur
     * 
     * @param string $nom     Le nom de la futur classe
     * @param array  $options Les options de la classe
     */
    public function __construct($nom, $options=array())
    {
        $this->_kernel = getKernel();
        
        $this->nom = $nom;
        
        if(isset($options['indente']))
        {
            $this->indente = $options['indente'];
        }
        
        if(isset($options['extends']))
        {
            $this->extends = $options['extends'];
        }
        
        if(isset($options['implements']))
        {
            $this->implements = $options['implements'];
        }
    }
    
    /**
     * Retourne le contenu de la futur classe
     * 
     * @return string La futur classe
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * Créer un attribut à la nouvelle classe
     * 
     * @param string $nom Le nom de l'attribut
     * @param array  $opt (default: array()) Les options de l'attribut : 
     * - string porter         : La porté de l'attribut. Par défaut à "protected"
     * - bool   get            : Si un get doit être créé. Par défaut à true
     * - bool   set            : Si un set doit être créé. Par défaut à true
     * - string type           : Le type de l'attribut. Par défaut aucun type prédéfini.
     * - mixed  default        : Valeur par défaut de l'attribut.
     * - bool   default_string : Permet d'indiqué que la valeur par défaut est de type string (met des ' autour.)
     * 
     * @TODO : Enlever default_string et repérer dynamiquement le type de la valeur.
     * 
     * @return bool True si réussi, False si existe déjà.
     */
    public function createAttribut($nom, $opt=array())
    {
        if(!in_array($nom, $this->attributs))
        {
            if(!isset($opt['porter']))
            {
                $opt['porter'] = 'protected';
            }
            
            if(!isset($opt['get']))
            {
                $opt['get'] = 1;
            }
            
            if(!isset($opt['set']))
            {
                $opt['set'] = 1;
            }
            
            $this->attributs[] = $nom;
            $this->attributs_porter[] = $opt['porter'];
            $this->attributs_option[] = $opt;
            
            if($opt['get'] == 1)
            {
                $this->get[] = $nom;
            }
            
            if($opt['set'] == 1)
            {
                $this->set[] = $nom;
            }
            
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Créer une nouvelle méthode pour la classe
     * 
     * @todo Gestion des arguments pour la méthode
     * 
     * @param string $nom    Le nom de la méthode
     * @param string $porter La porté de la méthode. Par défaut private.
     */
    public function createMethode($nom, $porter='private')
    {
        if(!in_array($nom, $this->methode))
        {
            $this->methode[] = $nom;
            $this->methode_porter[] = $porter;
        }
    }
    
    /**
     * Créer un attribut dans la futur classe.
     * 
     * @param int $key La clé de l'attribut à créer (tableau $this->attributs)
     */
    protected function genereAttribut($key)
    {
        $javadoc = $code = '';
        $default = isset($this->attributs_option[$key]['default']) ? true : false;
        
        $default_string =  false;
        if(
            isset($this->attributs_option[$key]['default_string']) && 
            $this->attributs_option[$key]['default_string'] == true
        )
        {
            $default_string =  true;
        }
        
        
        $javadoc .= $this->indente."/**\n";
        $javadoc .= $this->indente.' * @var ';
        $javadoc .= '$'.$this->attributs[$key].' : Ma description.';
        
        $code .= $this->indente;
        //Si un type à été déclaré
        $code .= (isset($this->attributs_option[$key]['type'])) ? '('.$this->attributs_option[$key]['type'].') ' : '';
        $code .= $this->attributs_porter[$key].' $'.$this->attributs[$key];
        
        //S'il y a une valeur par défaut
        if($default == true)
        {
            $javadoc .= ' Par défaut à ';
            $code .= ' = ';
            
            //Si la valeur par défaut est dite un string. On ajoute des '
            if($default_string == true)
            {
                $javadoc .= '\'';
                $code .= '\'';
            }
            
            $javadoc .= $this->attributs_option[$key]['default'];
            $code .= $this->attributs_option[$key]['default'];
            
            //Si la valeur par défaut est dite un string. On ajoute des '
            if($default_string == true)
            {
                $javadoc .= '\'';
                $code .= '\'';
            }
            
            $javadoc .= '.';
        }
        
        $javadoc .= "\n";
        $javadoc .= $this->indente." */ \n";
        
        $this->file .= $javadoc.$code.";\n\n";
    }
    
    /**
     * Créer un accesseur get
     * 
     * @param int $key La clé de la méthode à créer (tableau $this->get)
     */
    protected function genereGet($key)
    {
        $nom = $this->get[$key];
        
        $this->file .= $this->indente."/**\n";
        $this->file .= $this->indente.' * Accesseur get vers '.$nom."\n".$this->indente." * \n";
        $this->file .= $this->indente.' * @return mixed : La valeur de '.$nom."\n";
        $this->file .= $this->indente." */\n";
        
        $this->file .= $this->indente.'public function get_'.$nom.'() {return $this->'.$nom.';}'."\n\n";
        $this->methode_create[] = 'get_'.$nom."\n";
    }
    
    /**
     * Créer un accesseur set
     * 
     * @param int $key La clé de la méthode à créer (tableau $this->get)
     */
    protected function genereSet($key)
    {
        $nom = $this->set[$key];
        if(!in_array('set_'.$nom, $this->methode_create))
        {
            $this->file .= $this->indente."/**\n";
            $this->file .= $this->indente.' * Accesseur set vers '.$nom."\n".$this->indente." * \n";
            $this->file .= $this->indente.' * @param mixed : La nouvelle valeur de '.$nom."\n".$this->indente." * \n";
            $this->file .= $this->indente." * @return bool : True si réussi, False sinon.\n";
            $this->file .= $this->indente." */\n";
            
            $this->file .= $this->indente.'public function set_'.$nom.'($data) ';
            $this->file .= '{return ($this->'.$nom.' = $data) ? true : false;}'."\n\n";
            
            $this->methode_create[] = 'set_'.$nom;
        }
    }
    
    /**
     * Créer une méthode
     * 
     * @param int $key La clé de la méthode à créer (tableau $this->méthode)
     */
    protected function genereMethode($key)
    {
        if(!in_array($this->methode[$key], $this->methode_create))
        {
            $this->file .= $this->indente."/**\n";
            $this->file .= $this->indente." * Description de ma méthode.\n";
            $this->file .= $this->indente." */\n";
            
            $this->file .= $this->indente.$this->methode_porter[$key].' function '.$this->methode[$key].'() {}'."\n";
        }
    }
    
    /**
     * Lance la génération de la classe.
     * 
     * @return string La classe généré
     */
    public function genere()
    {
        //Création de la classe
        $this->file = "<?php\n";
        $this->file .= "/**\n * Ma description du fichier\n * @author me\n * @version 1.0\n */\n\n";
        $this->file .= "/**\n * La description de ma classe\n * @package MonProjet\n */\n";
        
        $this->file .= 'class '.$this->nom;
        if(!empty($this->extends))
        {
            $this->file .= ' extends '.$this->extends;
        }
        
        if(count($this->implements) > 0)
        {
            $this->file .= ' implements ';
            foreach($this->implements as $key => $implement)
            {
                $this->file .= ($key > 0) ? ', ' : '';
                $this->file .= $implement;
            }
        }
        $this->file .= "\n{\n";
        
        //Création des attributs
        foreach($this->attributs as $key => $attr)
        {
            $this->genereAttribut($key);
        }
        
        //Le constructeur
        $this->file .= "\n";
        $this->file .= $this->indente."/**\n";
        $this->file .= $this->indente." * Constructeur de la classe\n";
        $this->file .= $this->indente." */\n";
        $this->file .= $this->indente."public function __construct() {}\n\n";
        
        //Les gets
        foreach($this->get as $key => $nom)
        {
            $this->genereGet($key);
            if(in_array($nom, $this->set))
            {
                $this->genereSet($key);
            }
        }
        
        foreach($this->set as $key => $nom)
        {
            $this->genereSet($key);
        }
        
        foreach($this->methode as $key => $nom)
        {
            $this->genereMethode($key);
        }
        
        $this->file .= "}\n?>";
        return $this->file;
    }
}
?>
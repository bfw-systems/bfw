<?php
/**
 * Classes en rapport avec xml
 * @author Simon Willison
 * @author (to php5) Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFW;

/**
 * Permet de générer un fichier xml
 * @package bfw
 */
class XmlWriter_custom implements \BFWInterface\IXmlWriter_custom
{
    /**
     * @var $_kernel L'instance du Kernel
     */
    protected $_kernel;
    
    /**
     * @var $xml Le contenu xml
     */
    protected $xml;
    
    /**
     * @var $indent Par quoi on indente
     */
    protected $indent = ' ';
    
    /**
     * @var $stack Array contenant les balises entourat les balises d'éléments
     */
    protected $stack = array();
    
    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->_kernel = getKernel();
        
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
    }
    
    /**
     * Indente pour les "sous balise"
     */
    protected function _indent()
    {
        for($i = 0, $j = count($this->stack); $i < $j; $i++)
        {
            $this->xml .= $this->indent;
        }
    }
    
    /**
     * Créer une balise avec ces attributs (les balises principales, avec d'autres balise dedans en général)
     * 
     * @param string $element    Nom de la balise
     * @param array  $attributes (default: array()) Les attributs de la balise
     */
    public function push($element, $attributes = array())
    {
        $this->_indent();
        $this->xml .= '<'.$element;
        
        foreach ($attributes as $key => $value)
        {
            $this->xml .= ' '.$key.'="'.utf8_encode($value).'"';
        }
        
        $this->xml .= ">\n";
        $this->stack[] = $element;
    }
    
    /**
     * Créer une balise simple, avec ces attributs et son contenu
     * 
     * @param string $element    Nom de la balise
     * @param string $content    Le contenu de la balise
     * @param array  $attributes (default: array()) Les attributs de la balise
     */ 
    public function element($element, $content, $attributes = array())
    {
        $this->_indent();
        $this->xml .= '<'.$element;
        
        foreach ($attributes as $key => $value)
        {
            $this->xml .= ' '.$key.'="'.utf8_encode($value).'"';
        }
        
        $this->xml .= '>'.utf8_encode($content).'</'.$element.'>'."\n";
    }
    
    /**
     * Créer une balise avec ![CDATA pour mettre du xhtml dedans
     * 
     * @param string $element    Nom de la balise
     * @param string $content    Le contenu de la balise
     * @param array  $attributes (default: array()) Les attributs de la balise
     */
    public function element_cdata($element, $content, $attributes = array())
    {
        $this->_indent();
        $this->xml .= '<'.$element;
        
        foreach ($attributes as $key => $value)
        {
            $this->xml .= ' '.$key.'="'.utf8_encode($value).'"';
        }
        
        $this->xml .= '><![CDATA['.utf8_encode($content).']]></'.$element.'>'."\n";
    }
    
    /**
     * Créer une balise autofermante
     * 
     * @param string $element    Nom de la balise
     * @param array  $attributes (default: array()) Les attributs de la balise
     */
    public function emptyelement($element, $attributes = array())
    {
        $this->_indent();
        $this->xml .= '<'.$element;
        
        foreach ($attributes as $key => $value)
        {
            $this->xml .= ' '.$key.'="'.utf8_encode($value).'"';
        }
        
        $this->xml .= " />\n";
    }
    
    /**
     * Ferme une balise ouverte avec push()
     */
    public function pop()
    {
        $element = array_pop($this->stack);
        $this->_indent();
        $this->xml .= "</$element>\n";
    }
    
    /**
     * Retourne le résultat du xml
     * 
     * @return string Le xml
     */
    public function getXml()
    {
        return $this->xml;
    }
}
?>
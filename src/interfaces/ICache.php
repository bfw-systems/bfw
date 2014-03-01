<?php
/**
 * Interface en rapport avec la classe Cache
 * @author Vermeulen Maxime
 * @version 1.0
 */

namespace BFWInterface;

/**
 * Interface de la classe Cache
 * @package BFW
 */
interface ICache
{
    /**
     * Constructeur
     * 
     * @param string $controler : Le controleur qu'on doit lire
     */
    public function __construct($controler);
    
    /**
     * Lance la création du cache
     */
    public function run();
    
    /**
     * Accesseur vers l'attribut $controler
     * 
     * @param string $controler : Le controleur qu'on doit lire
     */
    public function set_controler($controler);
    
    /**
     * Si une erreur est trouvé. On l'affiche et on arrête le script.
     * 
     * @param string $txt : L'erreur à afficher.
     */
    protected function error($txt);
    
    /**
     * Partie 1 : Lecture du fichier php et mise sur 1 ligne dans une var.
     * Les commentaires sont supprimé dans le code en 1 ligne.
     */
    private function read();
    
    /**
     * Partie 2 : Recherche de la vue.
     */
    private function rechercheTpl();
    
    /**
     * Partie 3
     * Liste les block présent dans la vue
     */
    private function lstBlockView();
    
    /**
     * Partie 4
     * Recréer les EOL dans le code en 1 ligne (épuré des commentaires).
     * Remplace les méthodes de la classe Template par l'équivalent du cache.
     */
    private function createEOLEtAddVue();

    /**
     * Récupère le code html qui est entre le block indiqué en paramètre.
     * 
     * @param string $nomBlock : Le nom du block dont on doit retourner le contenu.
     *                           Par défault à null pour en dehors des blocks.
     */
    private function recupHtml($nomBlock=null);

    /**
     * Remplace toutes les balises <var /> par des variables contenant leurs valeurs
     * 
     * @param string $nomBlock : Le nom du block dont on doit retourner le contenu.
     *                           Par défault à null pour en dehors des blocks.
     * @param string $line     : La ligne qu'on lit
     * 
     * @return string La ligne avec les balises <var /> remplacé.
     */
    private function remplaceBaliseVar($nomBlock, $line);
    
    /**
     * Pour le cas où le système de Template n'est pas utilisé dans la page
     * On copie directement le code du controleur dans le cache.
     */
    protected function copyDirect2Cache();
    
    /**
     * Ecrit le code php du cache dans le fichier de cache
     * 
     * @param string $code : Le code à écrire dans la page
     */
    protected function WriteCache($code);
}
?>
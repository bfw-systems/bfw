<?php
/**
 * Classes en rapport avec le Kernel
 * @author Vermeulen Maxime <bulton.fr@gmail.com>
 * @version 1.0
 */

namespace BFW;

/**
 * Classe Noyau.
 * @package bfw
 */
class Kernel implements \SplSubject
{
    /**
     * @var array $observers Liste des observateurs instanciés
     */
    protected $observers = array();
    
    /**
     * @var bool $debug Si mode débug ou non.
     */
    protected $debug = false;
    
    /**
     * L'action à déclancher par les observers.
     */
    protected $notify_action = null;
    
    //******* Observateurs *******
    /**
     * Ajouter un nouvel observateur
     * 
     * @param SplObserver $observer L'observateur à ajouter
     */
    public function attach(SplObserver $observer)
    {
        $this->observers[] = $observer;
    }
    
    /**
     * Ajouter un nouvel observateur de type autre SplObserver
     * 
     * @param class $observer L'observateur à ajouter
     */
    public function attachOther($observer)
    {
        $this->observers[] = $observer;
    }
    
    /**
     * Enlever un observateur
     * 
     * @param SplObserver $observer L'observateur à enlever
     */
    public function detach(SplObserver $observer)
    {
        $key = array_search($observer, $this->observers);
        
        if($key)
        {
            unset($this->observers[$key]);
        }
    }
    
    /**
     * Enlever un observateur de type autre SplObserver
     * 
     * @param class $observer L'observateur à enlever
     */
    public function detachOther($observer)
    {
        $key = array_search($observer, $this->observers);
        
        if($key)
        {
            unset($this->observers[$key]);
        }
    }
    
    /**
     * Déclanche la notification vers les observers.
     * 
     * @param string $action L'action à faire par l'observateur.
     */
    public function notifyObserver($action)
    {
        foreach($this->observers as $observer)
        {
            //Appel la méthode update de l'observateur
            $observer->updateWithAction($this, $action);
        }
        
        $this->notify_action = null;
    }
    
    /**
     * Définie l'action à faire pour les observers.
     * 
     * @param string $action L'action à faire par l'observateur.
     * 
     * @return Kernel L'instance actuelle de la classe.
     */
    public function notifyAction($action)
    {
        $this->notify_action = $action;
        return $this;
    }
    
    /**
     * Déclanche la notification vers les observers.
     */
    public function notify()
    {
        if(!is_null($this->notify_action))
        {
            $this->notifyObserver($action);
        }
        else
        {
            foreach($this->observers as $observer)
            {
                //Appel la méthode update de l'observateur
                $observer->update($this);
            }
        }
    }
    //******* Observateurs *******
    
    /**
     * Set de l'attribut debug. Gestion de l'affichage des erreurs en plus.
     * 
     * @param bool $debug True si on est en mode débug, False sinon.
     */
    public function set_debug($debug)
    {
        $this->debug = $debug;
        
        if($debug) //Affiche toutes les erreurs de php
        {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
            ini_set('html_errors', true);
        }
        else //Affiche aucune erreur (prod)
        {
            error_reporting(0);
        }
    }
    
    /**
     * Accesseur vers l'attribut debug
     * 
     * @return bool
     */
    public function getDebug()
    {
        return $this->debug;
    }
}
?>
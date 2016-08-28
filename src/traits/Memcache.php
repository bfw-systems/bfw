<?php

namespace BFW\Traits;

trait Memcache
{
    /**
     * Permet de savoir si la clé existe
     * 
     * @param string $key la clé disignant les infos concernées
     * 
     * @throws \Exception Erreur dsans les paramètres donnée à la méthode
     * 
     * @return bool
     */
    public function ifExists($key)
    {
        $verifParams = \BFW\Helpers\Datas::checkTypes(
            [
                [
                    'type' => 'string',
                    'data' => $key
                ]
            ]
        );
        
        if (!$verifParams) {
            throw new \Exception('The $key parameters must be a string');
        }

        if ($this->get($key) === false) {
            return false;
        }

        return true;
    }

    /**
     * On modifie le temps avant expiration des infos sur
     * le serveur memcached pour une clé choisie.
     * 
     * @param string $key    la clé disignant les infos concerné
     * @param int    $expire le nouveau temps avant expiration
     *                          (0: pas d'expiration, max 30jours)
     * 
     * @throws \Exception Erreur dsans les paramètres donnée à la méthode
     * 
     * @return boolean|null
     */
    public function majExpire($key, $expire)
    {
        $verifParams = \BFW\Helpers\Datas::checkTypes(
            [
                ['type' => 'string', 'data' => $key],
                ['type' => 'int', 'data' => $expire]
            ]
        );

        if (!$verifParams) {
            throw new \Exception(
                'Once of parameters $key or $expire not have a correct type.'
            );
        }
        
        if(!$this->ifExists($key)) {
            return false;
        }

        $value = $this->get($key); //Récupère la valeur
        
        //On la "modifie" en remettant la même valeur mais en changeant
        //le temps avant expiration
        return $this->replace($key, $value, 0, $expire);
    }
}

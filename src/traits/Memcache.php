<?php

namespace BFW\Traits;

use \Exception;

/**
 * Trait to regroup memcache(d) methods
 */
trait Memcache
{
    /**
     * Define default value for server informations
     * 
     * @param array $infos Server informations
     * 
     * @return void
     * 
     * @throw \Exception If informations datas is not an array
     */
    protected function getServerInfos(&$infos)
    {
        if (!is_array($infos)) {
            throw new Exception(
                'Memcache(d) server information is not an array.'
            );
        }
        
        $infosKeyDefaultValues = [
            'host'       => null,
            'port'       => null,
            'weight'     => 0,
            'timeout'    => null,
            'persistent' => false
        ];
        
        foreach ($infosKeyDefaultValues as $infosKey => $defaultValue) {
            if (!isset($infos[$infosKey])) {
                $infos[$infosKey] = $defaultValue;
            }
        }
    }
    
    /**
     * Check if a key exists
     * 
     * @param string $key The memcache(d) key to check
     * 
     * @return boolean
     * 
     * @throws Exception If the key is not a string
     */
    public function ifExists($key)
    {
        $verifParams = \BFW\Helpers\Datas::checkType(
            [
                [
                    'type' => 'string',
                    'data' => $key
                ]
            ]
        );
        
        if (!$verifParams) {
            throw new Exception('The $key parameters must be a string');
        }

        if ($this->get($key) === false) {
            return false;
        }

        return true;
    }

    /**
     * Update the expire time for a memcache(d) key.
     * 
     * @param string $key The memcache(d) key to update
     * @param int $expire The new expire time
     * 
     * @return boolean
     * 
     * @throws Exception
     */
    public function majExpire($key, $expire)
    {
        $verifParams = \BFW\Helpers\Datas::checkType(
            [
                ['type' => 'string', 'data' => $key],
                ['type' => 'int', 'data' => $expire]
            ]
        );

        if (!$verifParams) {
            throw new Exception(
                'Once of parameters $key or $expire not have a correct type.'
            );
        }
        
        if (!$this->ifExists($key)) {
            throw new Exception(
                'The key '.$key.' not exist on memcache(d) server'
            );
        }

        $value = $this->get($key); //Récupère la valeur
        
        //On la "modifie" en remettant la même valeur mais en changeant
        //le temps avant expiration
        
        if (is_subclass_of($this, '\Memcache')) {
            return $this->replace($key, $value, 0, $expire);
        } elseif (is_subclass_of($this, '\Memcached')) {
            return $this->replace($key, $value, $expire);
        }
    }
}

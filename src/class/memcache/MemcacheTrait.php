<?php

namespace BFW\Memcache;

use \Exception;

/**
 * Trait to regroup memcache(d) methods
 */
trait MemcacheTrait
{
    /**
     * Read the server information and add not existing keys
     * 
     * @param array &$infos Server informations
     * 
     * @return void
     * 
     * @throw \Exception If informations datas is not an array
     */
    protected function completeServerInfos(&$infos)
    {
        if (!is_array($infos)) {
            throw new Exception(
                'Memcache(d) server information is not an array.',
                $this::ERR_SERVER_INFOS_FORMAT
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
     * addServer not created the connection. It's created at the first call
     * to the memcached servers.
     * 
     * So, we run the connect to all server declared
     * 
     * @throws \Exception If a server is not connected
     * 
     * @return boolean
     */
    protected function testConnect()
    {
        if ($this instanceof \BFW\Memcache\Memcache) {
            //With Memcache getStats not return stats for all connected server.
            $stats = $this->getExtendedStats();
        } else {
            $stats = $this->getStats();
        }
        
        if (!is_array($stats)) {
            throw new Exception(
                'No memcached server connected.',
                $this::ERR_NO_SERVER_CONNECTED
            );
        }
        
        foreach ($stats as $serverName => $serverStat) {
            if ($serverStat['uptime'] < 1) {
                throw new Exception(
                    'Memcached server '.$serverName.' not connected',
                    $this::ERR_A_SERVER_IS_NOT_CONNECTED
                );
            }
        }
        
        return true;
    }
    
    /**
     * Check if a key exists into memcache(d)
     * /!\ Not work if the correct value is the boolean false /!\
     * 
     * @param string $key The memcache(d) key to check
     * 
     * @return boolean
     * 
     * @throws Exception If the key is not a string
     */
    public function ifExists($key)
    {
        $verifParams = \BFW\Helpers\Datas::checkType([
            [
                'type' => 'string',
                'data' => $key
            ]
        ]);
        
        if (!$verifParams) {
            throw new Exception(
                'The $key parameters must be a string.'
                .' Currently the value is a/an '.gettype($key)
                .' and is equal to '.$key,
                $this::ERR_IFEXISTS_PARAM_TYPE
            );
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
    public function updateExpire($key, $expire)
    {
        $verifParams = \BFW\Helpers\Datas::checkType([
            ['type' => 'string', 'data' => $key],
            ['type' => 'int', 'data' => $expire]
        ]);

        if (!$verifParams) {
            throw new Exception(
                'Once of parameters $key or $expire not have a correct type.',
                $this::ERR_UPDATEEXPIRE_PARAM_TYPE
            );
        }
        
        if (!$this->ifExists($key)) {
            throw new Exception(
                'The key '.$key.' not exist on memcache(d) server',
                $this::ERR_KEY_NOT_EXIST
            );
        }

        //To change expire time, we need to re-set the value.
        $value = $this->get($key); //Get the value
        
        //Re-set the value with new expire time.
        if (is_subclass_of($this, '\Memcache')) {
            return $this->replace($key, $value, 0, $expire);
        } elseif (is_subclass_of($this, '\Memcached')) {
            return $this->replace($key, $value, $expire);
        }
    }
}

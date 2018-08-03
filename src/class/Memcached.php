<?php

namespace BFW;

use \Exception;

/**
 * Class to manage connection to memcache(d) server with memcached lib
 */
class Memcached extends \Memcached
{
    /**
     * @const ERR_SERVER_INFOS_FORMAT Exception code if server informations is
     * not in a correct format.
     */
    const ERR_SERVER_INFOS_FORMAT = 1105001;
    
    /**
     * @const ERR_NO_SERVER_CONNECTED Exception code if no server is connected.
     */
    const ERR_NO_SERVER_CONNECTED = 1105002;
    
    /**
     * @const ERR_A_SERVER_IS_NOT_CONNECTED Exception code if a server is not
     * connected.
     */
    const ERR_A_SERVER_IS_NOT_CONNECTED = 1105003;
    
    /**
     * @const ERR_IFEXISTS_PARAM_TYPE Exception code if a parameter type is not
     * correct into the method ifExists().
     */
    const ERR_IFEXISTS_PARAM_TYPE = 1105004;
    
    /**
     * @const ERR_UPDATEEXPIRE_PARAM_TYPE Exception code if a parameter type
     * is not correct into the method updateExpire().
     */
    const ERR_UPDATEEXPIRE_PARAM_TYPE = 1105005;
    
    /**
     * @const ERR_KEY_NOT_EXIST Exception code if the asked key not exist.
     * Actually only used into the method updateExpire().
     */
    const ERR_KEY_NOT_EXIST = 1105006;

    /**
     * @var array $config Config define into bfw config file for memcache(d)
     */
    protected $config;

    /**
     * Constructor.
     * Call parent constructor with the persistentId if declared in config
     * Connect to servers.
     */
    public function __construct()
    {
        $app          = \BFW\Application::getInstance();
        $this->config = $app->getConfig()->getValue(
            'memcached',
            'memcached.php'
        );
        
        if (!empty($this->config['persistentId'])) {
            parent::__construct($this->config['persistentId']);
        } else {
            parent::__construct();
        }
    }
    
    /**
     * Get accessor to the property config
     * 
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the list of server already connected (persistent)
     * Loop on server declared into the config file.
     * Connect to server if not already connected
     * 
     * @return void
     */
    public function connectToServers()
    {
        //Array for the list of server(s) to connect
        $addServers  = [];
        
        //Get all server already connected (persistent)
        $serversList = $this->generateServerList();
        
        //Loop on server declared into config
        foreach ($this->config['servers'] as $server) {
            $this->completeServerInfos($server);
            
            $host   = $server['host'];
            $port   = $server['port'];
            $weight = $server['weight'];
            
            //not check if port = (int) 0; Doc said to define to 0 for socket.
            if (empty($host) || $port === null) {
                continue;
            }
            
            //search if the reading server is not already connected
            if (in_array($host.':'.$port, $serversList)) {
                continue;
            }
            
            \BFW\Application::getInstance()
                ->getMonolog()
                ->getLogger()
                ->debug(
                    'Try to connect to memcached server.',
                    [
                        'host' => $host,
                        'port' => $port
                    ]
                );
            
            //If not, add the server at the list to connect
            $addServers[] = [$host, $port, $weight];
        }

        //Connect to server(s)
        $this->addServers($addServers);
        
        //Check if connect is successfull
        $this->testConnect();
    }
    
    /**
     * Get the list of servers where we are already connected (persistent)
     * 
     * @return string[]
     */
    protected function generateServerList()
    {
        $serversList = $this->getServerList();
        $servers     = [];
        
        foreach ($serversList as $serverInfos) {
            $servers[] = $serverInfos['host'].':'.$serverInfos['port'];
        }
        
        return $servers;
    }
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
                self::ERR_SERVER_INFOS_FORMAT
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
        $stats = $this->getStats();
        
        if (!is_array($stats)) {
            throw new Exception(
                'No memcached server connected.',
                self::ERR_NO_SERVER_CONNECTED
            );
        }
        
        foreach ($stats as $serverName => $serverStat) {
            if ($serverStat['uptime'] < 1) {
                throw new Exception(
                    'Memcached server '.$serverName.' not connected',
                    self::ERR_A_SERVER_IS_NOT_CONNECTED
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
                .' Currently the value is a/an '.gettype($key),
                self::ERR_IFEXISTS_PARAM_TYPE
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
                self::ERR_UPDATEEXPIRE_PARAM_TYPE
            );
        }
        
        if (!$this->ifExists($key)) {
            throw new Exception(
                'The key '.$key.' not exist on memcache(d) server',
                self::ERR_KEY_NOT_EXIST
            );
        }

        //To change expire time, we need to re-set the value.
        $value = $this->get($key); //Get the value
        
        //Re-set the value with new expire time.
        return $this->replace($key, $value, $expire); //We can use touch()
    }
}

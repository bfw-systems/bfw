<?php

namespace BFW\Memcache;

/**
 * Class to manage connection to memcache(d) server with memcached lib
 */
class Memcached extends \Memcached implements MemcacheInterface
{
    //Include Memcache trait to add some common methods with Memcache class
    use \BFW\Memcache\MemcacheTrait;
    
    /**
     * @const ERR_SERVER_INFOS_FORMAT Exception code if server informations is
     * not in a correct format.
     */
    const ERR_SERVER_INFOS_FORMAT = 1309001;
    
    /**
     * @const ERR_NO_SERVER_CONNECTED Exception code if no server is connected.
     */
    const ERR_NO_SERVER_CONNECTED = 1309002;
    
    /**
     * @const ERR_A_SERVER_IS_NOT_CONNECTED Exception code if a server is not
     * connected.
     */
    const ERR_A_SERVER_IS_NOT_CONNECTED = 1309003;
    
    /**
     * @const ERR_IFEXISTS_PARAM_TYPE Exception code if a parameter type is not
     * correct into the method ifExists().
     */
    const ERR_IFEXISTS_PARAM_TYPE = 1309004;
    
    /**
     * @const ERR_UPDATEEXPIRE_PARAM_TYPE Exception code if a parameter type
     * is not correct into the method updateExpire().
     */
    const ERR_UPDATEEXPIRE_PARAM_TYPE = 1309005;
    
    /**
     * @const ERR_KEY_NOT_EXIST Exception code if the asked key not exist.
     * Actually only used into the method updateExpire().
     */
    const ERR_KEY_NOT_EXIST = 1309006;

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
}

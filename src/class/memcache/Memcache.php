<?php

namespace BFW\Memcache;

use \Exception;

/**
 * Class to manage connection to memcache(d) server with memcache lib
 */
class Memcache extends \Memcache implements MemcacheInterface
{
    //Include Memcache trait to add some common methods with Memcached class
    use \BFW\Memcache\MemcacheTrait;
    
    /**
     * @const ERR_SERVER_INFOS_FORMAT Exception code if server informations is
     * not in a correct format.
     */
    const ERR_SERVER_INFOS_FORMAT = 1308001;
    
    /**
     * @const NO_SERVER_CONNECTED Exception code if no server is connected.
     */
    const ERR_NO_SERVER_CONNECTED = 1308002;
    
    /**
     * @const A_SERVER_IS_NOT_CONNECTED Exception code if a server is not
     * connected.
     */
    const ERR_A_SERVER_IS_NOT_CONNECTED = 1308003;
    
    /**
     * @const ERR_IFEXISTS_PARAM_TYPE Exception code if a parameter type is not
     * correct into the method ifExists().
     */
    const ERR_IFEXISTS_PARAM_TYPE = 1308004;
    
    /**
     * @const ERR_UPDATEEXPIRE_PARAM_TYPE Exception code if a parameter type
     * is not correct into the method updateExpire().
     */
    const ERR_UPDATEEXPIRE_PARAM_TYPE = 1308005;
    
    /**
     * @const ERR_KEY_NOT_EXIST Exception code if the asked key not exist.
     * Actually only used into the method updateExpire().
     */
    const ERR_KEY_NOT_EXIST = 1308006;
    
    /**
     * @const ERR_EXT_NOT_SUPPORTED Exception code if we try to use the
     * \Memcache class with PHP >= 7.0.0.
     */
    const ERR_EXT_NOT_SUPPORTED = 1308007;

    /**
     * @var array $config Config define into bfw config file for memcache(d)
     */
    protected $config;

    /**
     * Constructor.
     * Connect to servers
     * 
     * @throws Exception If PHP Version is >= 7.x (no memcache extension)
     */
    public function __construct()
    {
        //Check php version. No memcache lib for >= 7.x
        if (PHP_VERSION_ID > 70000) {
            throw new Exception(
                'PHP Memcache Extension is not supported on PHP >= 7.0.0',
                $this::ERR_EXT_NOT_SUPPORTED
            );
        }
        
        $app          = \BFW\Application::getInstance();
        $this->config = $app->getConfig()->getValue(
            'memcached',
            'memcached.php'
        );
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
     * Connect to memcache(d) server(s) defined on config
     * 
     * @return void
     */
    public function connectToServers()
    {
        //Loop on declared server(s)
        foreach ($this->config['servers'] as $server) {
            $this->completeServerInfos($server);
            
            $host       = $server['host'];
            $port       = $server['port'];
            $weight     = $server['weight'];
            $timeout    = $server['timeout'];
            $persistent = $server['persistent'];
            
            //Not checked if port = (int) 0; Doc said to define to 0 for socket
            if (empty($host) || $port === null) {
                continue;
            }
            
            //Error "Memcache::addserver(): weight must be a positive integer"
            if ($weight === 0) {
                $weight = 1;
            }
            
            //If a timeout is declared
            //I not found the default value for this parameters, so an if...
            if ($timeout !== null) {
                $this->addServer($host, $port, $persistent, $weight, $timeout);
                continue;
            }
            
            $this->addServer($host, $port, $persistent, $weight);
        }
        
        //Check if connect is successfull
        $this->testConnect();
    }
}

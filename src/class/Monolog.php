<?php

namespace BFW;

use \Exception;

/**
 * Class to read monolog config file and instanciate monolog from config
 */
class Monolog
{
    /**
     * @const ERR_HANDLERS_LIST_FORMAT Exception code if config for handlers
     * list have not a correct format
     */
    const ERR_HANDLERS_LIST_FORMAT = 1318001;
    
    /**
     * @const ERR_HANDLER_INFOS_FORMAT Exception code if the handler infos is
     * not in a correct format
     */
    const ERR_HANDLER_INFOS_FORMAT = 1318002;
    
    /**
     * @const ERR_HANDLER_INFOS_MISSING_NAME Exception code if a handler not
     * have declared name
     */
    const ERR_HANDLER_INFOS_MISSING_NAME = 1318003;
    
    /**
     * @const ERR_HANDLER_NAME_NOT_A_STRING Exception code if a handler name
     * value is not a string
     */
    const ERR_HANDLER_NAME_NOT_A_STRING = 1318004;
    
    /**
     * @const ERR_HANDLER_CLASS_NOT_FOUND Exception code if a handler class
     * has not been found
     */
    const ERR_HANDLER_CLASS_NOT_FOUND = 1318005;
    
    /**
     * @var string $channelName The monolog channel name
     */
    protected $channelName = '';
    
    /**
     * @var \BFW\Config $config The config object containing the handlers list
     */
    protected $config;
    
    /**
     * @var \Monolog\Logger $monolog The monolog logger object
     */
    protected $monolog;
    
    /**
     * @var array $handlers List of all declared handler
     */
    protected $handlers = [];
    
    /**
     * Populate properties
     * Initialize monolog logger object
     * 
     * @param string $channelName The monolog channel name
     * @param \BFW\Config $config The config object containing handlers list
     */
    public function __construct($channelName, \BFW\Config $config)
    {
        $this->channelName = (string) $channelName;
        $this->config      = $config;
        $this->monolog     = new \Monolog\Logger($this->channelName);
    }
    
    /**
     * Get accessor to property channelName
     * 
     * @return string
     */
    public function getChannelName()
    {
        return $this->channelName;
    }
    
    /**
     * Get accessor to property config
     * 
     * @return \BFW\Config
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Get accessor to property monolog
     * 
     * @return \Monolog\Logger
     */
    public function getMonolog()
    {
        return $this->monolog;
    }
    
    /**
     * Get accessor to property handlers
     * 
     * @return array
     */
    public function getHandlers()
    {
        return $this->handlers;
    }
    
    /**
     * Adding all handlers to monolog logger
     * 
     * @param string $configKeyName The key name containing handlers list
     * @param string $configFileName The config file name containing handlers
     * list
     * 
     * @throws \Exception
     * 
     * @return void
     */
    public function addAllHandlers(
        $configKeyName = 'handlers',
        $configFileName = 'monolog.php'
    ) {
        $handlers = $this->config->getValue($configKeyName, $configFileName);
        
        if (is_object($handlers)) {
            $handlers = [$handlers];
        }
        
        if (!is_array($handlers)) {
            throw new Exception(
                'Handlers list into monolog config file should be an array.',
                self::ERR_HANDLERS_LIST_FORMAT
            );
        }
        
        foreach ($handlers as $handlerInfos) {
            $this->addNewHandler($handlerInfos);
        }
    }
    
    /**
     * Check and add a new handler to the logger
     * 
     * @param \stdObject $handlerInfos Handler infos (name and args)
     * 
     * @throws \Exception
     * 
     * @return void
     */
    protected function addNewHandler($handlerInfos)
    {
        $this->checkHandlerInfos($handlerInfos);
        
        $handlerClassName = $handlerInfos->name;
        $handler          = new $handlerClassName(...$handlerInfos->args);
        
        $this->handlers[] = $handler;
        $this->monolog->pushHandler($handler);
    }
    
    /**
     * Check the handler infos
     * 
     * @param \stdObject $handlerInfos Handler infos (name and args)
     * 
     * @throws \Exception
     * 
     * @return void
     */
    protected function checkHandlerInfos($handlerInfos)
    {
        if (!is_object($handlerInfos)) {
            throw new Exception(
                'the handler infos should be an object.',
                self::ERR_HANDLER_INFOS_FORMAT
            );
        }
        
        $this->checkHandlerName($handlerInfos);
        $this->checkHandlerArgs($handlerInfos);
    }
    
    /**
     * Check the handler name
     * 
     * @param \stdObject $handlerInfos Handler infos (name and args)
     * 
     * @throws \Exception
     * 
     * @return void
     */
    protected function checkHandlerName($handlerInfos)
    {
        if (!property_exists($handlerInfos, 'name')) {
            throw new Exception(
                'The handler infos should have the property name',
                self::ERR_HANDLER_INFOS_MISSING_NAME
            );
        }
        
        if (!is_string($handlerInfos->name)) {
            throw new Exception(
                'The handler name should be a string.',
                self::ERR_HANDLER_NAME_NOT_A_STRING
            );
        }
        
        if (!class_exists($handlerInfos->name)) {
            throw new Exception(
                'The class '.$handlerInfos->name.' has not been found.',
                self::ERR_HANDLER_CLASS_NOT_FOUND
            );
        }
    }
    
    /**
     * Check the handler arguments list
     * 
     * @param \stdObject $handlerInfos Handler infos (name and args)
     * 
     * @return void
     */
    protected function checkHandlerArgs($handlerInfos)
    {
        if (!property_exists($handlerInfos, 'args')) {
            $handlerInfos->args = [];
        }
        
        if (!is_array($handlerInfos->args)) {
            $handlerInfos->args = [$handlerInfos->args];
        }
    }
}

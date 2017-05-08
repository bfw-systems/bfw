<?php

namespace BFW\Install\test\unit\mocks;

/**
 * Mocked ModuleInstall class
 */
class ModuleInstall extends \BFW\Install\ModuleInstall
{
    /**
     * @var array|\stdClass $forcedInfos Mocked module informations
     */
    protected $forcedInfos = [];
    
    /**
     * @var boolean $removeDirectoryStatus Mocked value returned when call
     *  removeDirectory method
     */
    public static $removeDirectoryStatus = true;
    
    /**
     * Force new information about the module
     * 
     * @param array|\stdClass $newInfos The new informations about the module
     */
    public function forceInfos($newInfos)
    {
        $this->forcedInfos = $newInfos;
    }
    
    /**
     * {@inheritdoc}
     * Return datas declared into the property forcedInfos
     */
    protected function getInfosFromModule()
    {
        if (!is_object($this->forcedInfos)) {
            $this->forcedInfos = (object) $this->forcedInfos;
        }
        
        return $this->forcedInfos;
    }
    
    /**
     * Magic getter
     * 
     * @link http://php.net/manual/en/language.oop5.overloading.php#object.get
     * 
     * @param string $propertyName The property name
     * 
     * @return mixed
     * 
     * @throws \Exception If the property not exist
     */
    public function __get($propertyName)
    {
        if (!property_exists($this, $propertyName)) {
            throw new \Exception($propertyName.' is not a property.');
        }
        
        return $this->{$propertyName};
    }
    
    /**
     * {@inheritdoc}
     * Return value of the property removeDirectoryStatus
     */
    protected static function removeDirectory($dirPath)
    {
        return self::$removeDirectoryStatus;
    }
}

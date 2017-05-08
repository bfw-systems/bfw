<?php

namespace BFW\Memcache\test\unit\mocks;

/**
 * Mock for Memcache class
 */
class Memcache extends \BFW\Memcache\Memcache
{
    /**
     * Method to call the protected method getServerInfos
     * 
     * @param array &$infos Server informations
     * 
     * @return void
     */
    public function callGetServerInfos(&$infos)
    {
        return parent::getServerInfos($infos);
    }
}

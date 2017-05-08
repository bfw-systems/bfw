<?php

namespace BFW\Memcache\test\unit\mocks;

/**
 * Mock for Memcached class
 */
class Memcached extends \BFW\Memcache\Memcached
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

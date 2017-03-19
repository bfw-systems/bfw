<?php

namespace BFW\Memcache\test\unit\mocks;

class Memcached extends \BFW\Memcache\Memcached
{
    use \BFW\test\helpers\Application;
    
    public function callGetServerInfos(&$infos)
    {
        return parent::getServerInfos($infos);
    }
}

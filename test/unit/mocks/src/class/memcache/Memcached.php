<?php

namespace BFW\Memcache\test\unit\mocks;

class Memcached extends \BFW\Memcache\Memcached
{
    use \BFW\test\helpers\ApplicationInit;
    
    public function callGetServerInfos(&$infos)
    {
        return parent::getServerInfos($infos);
    }
}

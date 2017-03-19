<?php

namespace BFW\Memcache\test\unit\mocks;

class Memcached extends \BFW\Memcache\Memcached
{
    public function callGetServerInfos(&$infos)
    {
        return parent::getServerInfos($infos);
    }
}

<?php

namespace BFW\Memcache\test\unit\mocks;

class Memcache extends \BFW\Memcache\Memcache
{
    public function callGetServerInfos(&$infos)
    {
        return parent::getServerInfos($infos);
    }
}

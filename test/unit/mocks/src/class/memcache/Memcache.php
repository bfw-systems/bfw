<?php

namespace BFW\Memcache\test\unit\mocks;

class Memcache extends \BFW\Memcache\Memcache
{
    use \BFW\test\helpers\ApplicationInit;
    
    public function callGetServerInfos(&$infos)
    {
        return parent::getServerInfos($infos);
    }
}

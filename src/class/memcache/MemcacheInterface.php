<?php

namespace BFW\Memcache;

interface MemcacheInterface
{
    public function getConfig();
    
    public function ifExists($key);
    
    public function updateExpire($key, $expire);
}

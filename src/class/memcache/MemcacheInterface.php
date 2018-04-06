<?php

namespace BFW\Memcache;

interface MemcacheInterface
{
    public function ifExists($key);
    
    public function updateExpire($key, $expire);
}

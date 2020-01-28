<?php

namespace TodoApp\Cache;

use TodoApp\Cache\Repository;

class Cache implements Repository
{
    private $storage;

    function __construct(string $host, int $port)
    {
        $this->storage = new \Memcache();
        $this->storage->connect($host, $port);
    }

    public function __destruct()
    {
        $this->storage->close();
    }

    public function delete($key)
    {
        $this->storage->delete($key);
    }

    public function get($key)
    {
        return $this->storage->get($key);
    }

    public function has($key) : bool
    {
        if ($this->get($key) === false)
        {
            return false;
        }
        
        return true;
    }

    public function set($key, $value)
    {
        $this->storage->set($key, $value);
    }
}

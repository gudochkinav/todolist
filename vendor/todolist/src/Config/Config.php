<?php

namespace TodoApp\Config;

use TodoApp\Config\Repository;

class Config implements Repository
{
    private $items = [];

    function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function all() : array
    {
        return $this->items;
    }

    public function get($key, $default = null)
    {
        if (is_null($key))
        {
            return $this->items;
        }

        if (array_key_exists($key, $this->items))
        {
            return $this->items[$key];
        }

        if (strpos($key, '.') === false)
        {
            return $this->items[$key] ?? $default;
        }

        $array = $this->items;

        foreach(explode('.', $key) as $segment)
        {
            if (is_array($array[$segment]) || array_keys($array, $segment))
            {
                $array = $array[$segment];

                continue;
            }

            return $array[$segment];
        }

        return $array;
    }

    public function has($key) : bool
    {
        if (is_null($key))
        {
            return false;
        }
        
        if (array_keys($this->items, $key))
        {
            return true;
        }

        if (strpos($key, '.') === false)
        {
            return false;
        }
        
        $array = $this->items;
        
        foreach (explode('.', $key) as $segment)
        {
            if (array_key_exists($segment, $array) || is_array($array[$segment]))
            {
                $array = $array[$segment];

                continue;
            }

            return false;
        }

        return true;
    }

    public function set($items) : void
    {
        $this->items = $items;
    }
}

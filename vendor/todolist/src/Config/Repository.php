<?php

namespace TodoApp\Config;

interface Repository
{
    public function has($key);

    public function get($key, $default = null);

    public function set($items);

    public function all();
}

<?php

namespace TodoApp\Interfaces;

interface ContainerEngine 
{
    public function clear();
    
    public function push($key, $value);
    
    public function get($key);
    
    public function has($key);
}

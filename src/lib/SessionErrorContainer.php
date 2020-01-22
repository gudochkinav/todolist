<?php

namespace TodoApp;

use TodoApp\Interfaces\ContainerEngine;

class SessionErrorContainer implements ContainerEngine
{
    private static $instance;
    
    private function __construct() 
    {
        if (!isset($_SESSION['errors']))
        {
            $_SESSION['errors'] = [];
        }
    }
    
    private function __clone() {}

    public static function getInstance() 
    {
        if (!self::$instance) 
        {
            self::$instance = new SessionErrorContainer();
        }

        return self::$instance;
    }

    public function get($key)
    {
        if (!isset($_SESSION['errors'][$key]))
        {
            return null;
        }

        $error = $_SESSION['errors'][$key];
        unset($_SESSION['errors'][$key]);

        return $error;
    }

    public function has($key)
    {
        if (!isset($_SESSION['errors'][$key])) 
        {
            return false;
        }
        
        return true;
    }

    public function push($key, $value) 
    {
        $_SESSION['errors'][$key] = $value;
    }

    public function clear() 
    {
        unset($_SESSION['errors']);
    }
    
    public function getAll() 
    {
        $errors = $_SESSION['errors'];
        $this->clear();

        return $errors;
    }
}

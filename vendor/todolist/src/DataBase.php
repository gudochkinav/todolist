<?php

namespace TodoApp;

class DataBase 
{
    private static $instance;

    protected $config;
    
    protected $connect;
    
    private function __construct() 
    {
        $this->loadConfigFiles();
        $this->connect();
    }

    private function __clone() {}
    
    public function getInstance() 
    {
        if (!self::$instance)
        {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    protected function loadConfigFiles() 
    {
        $this->config = include(BASE_DIR . "config/database.php");
    }

    protected function connect() 
    {
        $this->connect = new \PDO('mysql:host=' . $this->config['host'] . ';dbname=' . $this->config['databasename'], $this->config['username'], $this->config['password']);
    }

    public function getConnect() 
    {
        return $this->connect;
    }
}

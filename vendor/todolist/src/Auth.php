<?php

namespace TodoApp;

use App\Repositories\UserRepository;

class Auth
{
    private static $instance;

    private $userRepository;
    
    private function __construct() 
    {
        $this->userRepository = new UserRepository();
    }
    
    private function __clone() {}

    public static function getInstance() 
    {
        if (!self::$instance)
        {
            self::$instance = new Auth();
        }
        
        return self::$instance;
    }
    
    public function user() 
    {
        if (!$this->check())
        {
            return null;
        }

        return $this->userRepository->getUserByEmail($_SESSION['email']);
    }
    
    public function check() : bool
    {
        if (!isset($_SESSION['email']))
        {
            return false;
        }

        return true;
    }

    public function login(string $email) : void
    {
        $_SESSION['email'] = $email;
        $_SESSION['logged_at'] = time();
    }
    
    public function logout() : void
    {
        session_destroy();
        unset($_SESSION);
    }
}

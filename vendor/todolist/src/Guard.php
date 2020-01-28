<?php

namespace TodoApp;

class Guard 
{
    const SALT = '$5$rounds=5000$sbvNZ0utMV9p00A5VYs7H8gIlMMwk8HR$';

    public function crypt(string $string, string $salt = '') : string
    {
        if (!$salt)
        {
            $salt = self::SALT;
        }

        return crypt($string, $salt);
    }
    
    public function compareHash(string $string, string $hashedString, string $salt = '') : bool
    {
        if ($hashedString == $this->crypt($string, $salt)) 
        {
            return true;
        }
        
        return false;
    }
}

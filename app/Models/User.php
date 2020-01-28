<?php

namespace App\Models;

class User 
{
    private $id;
    
    private $name;

    private $email;
    
    private $password;
    
    function __construct(array $params)
    {
        $this->id = $params['id'];
        $this->name = $params['name'];
        $this->email = $params['email'];
        $this->password = $params['password'];
    }

    public function getId(): int
    {
        return $this->id;
    }
    
    public function getName(): string 
    {
        return $this->name;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function getPassword() : string 
    {
        return $this->password;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $password,
        ];
    }
}

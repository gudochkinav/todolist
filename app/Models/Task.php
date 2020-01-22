<?php

namespace App\Models;

class Task 
{
    const OPENED_TASK = '1';
    
    const CLOSED_TASK = '0';
    
    private $id;
    
    private $name;
    
    private $userId;
    
    private $status;
    
    private $createdAt;
    
    private $updatedAt;

    function __construct(int $id, string $name, int $userId, int $status, int $createdAt, int $updatedAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
    
    public function getId() : int
    {
        return $this->id;
    }

    public function getName() : string 
    {
        return $this->name;
    }
    
    public function setName(string $name) : void
    {
        $this->name = $name;
    }
    
    public function getUserId() : int
    {
        return $this->userId;
    }
    
    public function getStatus() : int
    {
        return $this->status;
    }
    
    public function setStatus(int $status) : void
    {
        $this->status = $status;
    }

    public function isActive() : bool
    {
        if ($this->status === self::OPENED_TASK)
        {
            return true;
        }
        
        return false;
    }
}
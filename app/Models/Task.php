<?php

namespace App\Models;

class Task
{
    public const OPENED_TASK = '1';

    public const CLOSED_TASK = '0';

    private $id;

    private $name;

    private $userId;
    
    private $status;
    
    private $createdAt;
    
    private $updatedAt;

    function __construct(array $params)
    {
        $this->id = $params['id'];
        $this->name = $params['name'];
        $this->userId = $params['user_id'];
        $this->status = $params['status'];
        $this->createdAt = $params['created_at'];;
        $this->updatedAt = $params['updated_at'];;
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
    
    public function close() : void
    {
        $this->status = self::CLOSED_TASK;
    }
    
    public function open() : void
    {
        $this->status = self::OPENED_TASK;
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
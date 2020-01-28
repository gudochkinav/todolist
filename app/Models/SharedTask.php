<?php

namespace App\Models;

use App\Models\User;
use App\Repositories\UserRepository;

class SharedTask
{
    const READ_MODE = 'r';
    
    const WRITE_MODE = 'w';

    protected $id;
    
    protected $ownerId;
    
    protected $sharedUserId;

    protected $mode;

    protected $hash;
    
    public function __construct(array $params)
    {
        $this->id = $params['id'];
        $this->ownerId = $params['owner_id'];
        $this->sharedUserId = $params['shared_user_id'];
        $this->mode = $params['mode'];
        $this->hash = $params['hash'];
    }
    
    public function toggleMode() : void
    {
        if ($this->mode == self::READ_MODE)
        {
            $this->mode = self::WRITE_MODE;
        }
        else 
        {
            $this->mode = self::READ_MODE;
        }
    }
    
    public function getOwnerUser() : User
    {
        $userRepository = new UserRepository();
        return $userRepository->getUserById($this->ownerId);
    }
    
    public function getSharedUser() : User
    {
        $userRepository = new UserRepository();
        return $userRepository->getUserById($this->sharedUserId);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $hash): void
    {
        $this->id = $id;
    }

    public function setMode(string $mode): void
    {
        if ($mode == self::WRITE_MODE)
        {
            $this->mode = self::WRITE_MODE;
        }

        $this->mode = self::READ_MODE;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setOwnerId(int $ownerId): void
    {
        $this->ownerId = $ownerId;
    }

    public function getOwnerId() : int
    {
        return $this->ownerId;
    }

    public function setSharedUserId(int $sharedUserId) : void
    {
        $this->sharedUserId = $sharedUserId;
    }
    
    public function getSharedUserId(): int
    {
        return $this->sharedUserId;
    }

    public function getHash() : string
    {
        return $this->hash;
    }
    
    public function setHash(string $hash) : void
    {
        $this->hash = $hash;
    }
}
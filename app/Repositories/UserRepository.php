<?php

namespace App\Repositories;

use TodoApp\DataBase;
use App\Models\User;

class UserRepository
{
    protected const TABLE_NAME = 'users';

    protected $database;

    protected $tableName;

    public function __construct()
    {
        $this->tableName = self::TABLE_NAME;
        $this->database = DataBase::getInstance();
    }
    
    public function getTableName() : string
    {
        return $this->tableName;
    }
    
    public function existsUser(string $email) : bool
    {
        $stmp = $this->database->getConnect()->prepare('SELECT * from ' . $this->getTableName() . ' where email=:email');

        $stmp->bindParam(':email', $email);
        $stmp->execute();

        $result = $stmp->fetch();
        
        if (!$result)
        {
            return false;
        }
        
        return true;
    }
    
    public function getUserById(int $id) : User
    {
        $stmp = $this->database->getConnect()->prepare('SELECT * from ' . $this->getTableName() . ' where id=:id');

        $stmp->bindParam(':id', $id);
        $stmp->execute();
        $result = $stmp->fetch();

        $params['id'] = $result['id'];
        $params['name'] = $result['name'];
        $params['email'] = $result['email'];
        $params['password'] = $result['password'];

        return new User($params);
    }

    public function getUserByEmail(string $email) : ?User
    {
        $stmp = $this->database->getConnect()->prepare('SELECT * from ' . $this->getTableName() . ' where email=:email');

        $stmp->bindParam(':email', $email);
        $stmp->execute();

        $result = $stmp->fetch();
        if ( ! $result)
        {
            return null;
        }

        $params['id'] = $result['id'];
        $params['name'] = $result['name'];
        $params['email'] = $result['email'];
        $params['password'] = $result['password'];
        
        return new User($params);
    }
    
    public function createUser(string $name, string $email, string $password) 
    {
        $insertTime = time();
        $stmp = $this->database->getConnect()->prepare('insert into ' . $this->getTableName() . ' (email, name, password, created_at, updated_at) values (:email, :name, :password, :created_at, :updated_at)');

        $stmp->bindParam(':name', $name);
        $stmp->bindParam(':email', $email);
        $stmp->bindParam(':password', $password);
        $stmp->bindParam(':created_at', $insertTime);
        $stmp->bindParam(':updated_at', $insertTime);

        $result = $stmp->execute();

        error_log($this->database->getConnect()->errorInfo());

        return $this->getUserByEmail($email);
    }
}
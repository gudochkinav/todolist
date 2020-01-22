<?php

namespace App\Repositories;

use TodoApp\DataBase;
use App\Models\User;

class UserRepositories 
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
    
    public function getUserByEmail(string $email)
    {
        $stmp = $this->database->getConnect()->prepare('SELECT * from ' . $this->getTableName() . ' where email=:email');

        $stmp->bindParam(':email', $email);
        $stmp->execute();
        
        return $stmp->fetch();
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
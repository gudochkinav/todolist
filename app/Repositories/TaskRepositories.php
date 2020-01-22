<?php

namespace App\Repositories;

use TodoApp\DataBase;
use App\Models\Task;

class TaskRepositories
{
    protected const TABLE_NAME = 'tasks';

    protected $database;
    
    protected $tableName;

    function __construct() 
    {
        $this->tableName = self::TABLE_NAME;
        $this->database = DataBase::getInstance();
    }
    
    public function getTableName() : string
    {
        return $this->tableName;
    }

    public function closeAll() 
    {
        $updateTime = time();
        $status = '0';

        $stmp = $this->database->getConnect()->prepare('update ' . $this->getTableName() . ' set status=:status, updated_at=:updated_at');
        $stmp->bindParam(':status', $status);
        $stmp->bindParam(':updated_at', $updateTime);

        $stmp->execute();
    }
    
    public function closeTask(int $id) 
    {
        $updateTime = time();
        $status = '0';

        $stmp = $this->database->getConnect()->prepare('update ' . $this->getTableName() . ' set status=:status, updated_at=:updated_at where id=:id');

        $stmp->bindParam(':id', $id);
        $stmp->bindParam(':status', $status);
        $stmp->bindParam(':updated_at', $updateTime);

        $stmp->execute();
    }

    public function openTask(int $id)
    {
        $updateTime = time();
        $status = '1';

        $stmp = $this->database->getConnect()->prepare('update ' . $this->getTableName() . ' set status=:status, updated_at=:updated_at where id=:id');

        $stmp->bindParam(':id', $id);
        $stmp->bindParam(':status', $status);
        $stmp->bindParam(':updated_at', $updateTime);

        $stmp->execute();
    }
    
    public function openAll() 
    {
        $updateTime = time();
        $status = '1';

        $stmp = $this->database->getConnect()->prepare('update ' . $this->getTableName() . ' set status=:status, updated_at=:updated_at');

        $stmp->bindParam(':status', $status);
        $stmp->bindParam(':updated_at', $updateTime);

        $stmp->execute();
    }
    
    public function deleteTask(int $id, int $user_id) 
    {
        $stmp = $this->database->getConnect()->prepare('delete from ' . $this->getTableName() . ' where id=:id and user_id=:user_id');

        $stmp->bindParam(':id', $id);
        $stmp->bindParam(':user_id', $user_id);

        $stmp->execute();
    }
    
    public function insert(string $name, int $user_id, int $status = Task::OPENED_TASK) : int
    {
        $insertTime = time();
        $stmp = $this->database->getConnect()->prepare('insert into ' . $this->getTableName() . ' (name, user_id, created_at, updated_at) values (:name, :user_id, :created_at, :updated_at)');

        $stmp->bindParam(':name', $name);
        $stmp->bindParam(':user_id', $user_id);
        $stmp->bindParam(':created_at', $insertTime);
        $stmp->bindParam(':updated_at', $insertTime);

        $stmp->execute();
        
        return $this->database->getConnect()->lastInsertId();
    }
    
    public function getUserList(int $user_id) 
    {
        $result = [];
        $stmp = $this->database->getConnect()->prepare('SELECT * from ' . $this->getTableName() . ' where user_id=:user_id order by created_at desc');
        $stmp->bindParam(':user_id', $user_id);
        $stmp->execute();

        $list = $stmp->fetchAll();

        foreach ($list as $row) {
            $result[] = $row;
        }

        return $result;
    }

    public function getAll() : array
    {
        $result = [];
        $list = $this->database->getConnect()->query('SELECT * from ' . $this->getTableName() . ' order by created_at desc');
        foreach ($list as $row) 
        {
            $result[] = $row;
        }

        return $result;
    }

    public function getTask(int $id, int $user_id) 
    {
        $stmp = $this->database->getConnect()->prepare('SELECT * from ' . $this->getTableName() . ' where id=:id and user_id=:user_id');

        $stmp->bindParam(':id', $id);
        $stmp->bindParam(':user_id', $user_id);
        $stmp->execute();
        
        return $stmp->fetch();
    }
    
    public function updateTask(array $task)
    {
        $updateTime = time();
        $stmp = $this->database->getConnect()->prepare('update ' . $this->getTableName() . ' set name=:name, status=:status, updated_at=:updated_at where id=:id');

        $stmp->bindParam(':id', $task['id']);
        $stmp->bindParam(':name', $task['name']);
        $stmp->bindParam(':status', $task['status']);
        $stmp->bindParam(':updated_at', $updateTime);

        $stmp->execute();
    }
}

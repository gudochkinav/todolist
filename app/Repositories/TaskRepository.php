<?php

namespace App\Repositories;

use TodoApp\DataBase;
use App\Models\Task;

class TaskRepository
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

    public function closeAll(int $user_id) 
    {
        $updateTime = time();
        $status = '0';
        
        $stmp = $this->database->getConnect()->prepare('update ' . $this->getTableName() . ' set status=:status, updated_at=:updated_at where user_id=:user_id');
        $stmp->bindParam(':status', $status);
        $stmp->bindParam(':updated_at', $updateTime);
        $stmp->bindParam(':user_id', $user_id);

        $stmp->execute();
    }

    public function openAll(int $user_id) 
    {
        $updateTime = time();
        $status = '1';

        $stmp = $this->database->getConnect()->prepare('update ' . $this->getTableName() . ' set status=:status, updated_at=:updated_at where user_id=:user_id');

        $stmp->bindParam(':status', $status);
        $stmp->bindParam(':updated_at', $updateTime);
        $stmp->bindParam(':user_id', $user_id);

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

        foreach ($list as $row) 
        {
            $params['id'] = $row['id'];
            $params['name'] = $row['name'];
            $params['user_id'] = $row['user_id'];
            $params['status'] = $row['status'];
            $params['created_at'] = $row['created_at'];
            $params['updated_at'] = $row['updated_at'];

            $result[] = new Task($params);
        }

        return $result;
    }

    public function getAll() : array
    {
        $result = [];
        $list = $this->database->getConnect()->query('SELECT * from ' . $this->getTableName() . ' order by created_at desc');
        foreach ($list as $row) 
        {
            $params['id'] = $row['id'];
            $params['name'] = $row['name'];
            $params['user_id'] = $row['user_id'];
            $params['status'] = $row['status'];
            $params['created_at'] = $row['created_at'];
            $params['updated_at'] = $row['updated_at'];
            
            $result[] = new Task($params);
        }

        return $result;
    }

    public function getTask(array $params) 
    {
        $result = [];

        if (empty($params))
        {
            return null;
        }

        $whereConditions = [];
        $whereParams = [];

        if (isset($params['id']))
        {
            $whereConditions[] = 'id = ?';
            $whereParams[] = $params['id'];
        }

        if (isset($params['user_id']))
        {
            $whereConditions[] = 'user_id = ?';
            $whereParams[] = $params['user_id'];
        }

        if (empty($whereConditions))
        {
            return null;
        }

        $where = implode('AND', $whereConditions);

        $stmp = $this->database->getConnect()->prepare('SELECT * from ' . $this->getTableName() . ' where ' . $where);

        $stmp->execute($whereParams);

        $tasks = $stmp->fetchAll();
        
        foreach ($tasks as $task)
        {
            $result[] = new Task($task);
        }
        
        return $result;
    }
    
    public function updateTask(Task $task)
    {
        $updateTime = time();
        $stmp = $this->database->getConnect()->prepare('update ' . $this->getTableName() . ' set name=:name, status=:status, updated_at=:updated_at where id=:id');

        $stmp->bindParam(':id', $task->getId());
        $stmp->bindParam(':name', $task->getName());
        $stmp->bindParam(':status', $task->getStatus());
        $stmp->bindParam(':updated_at', $updateTime);

        $stmp->execute();
    }
}

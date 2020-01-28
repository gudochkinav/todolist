<?php

namespace App\Repositories;

use TodoApp\DataBase;
use App\Models\SharedTask;

class SharedTaskRepository
{
    protected const TABLE_NAME = 'shared_tasks';

    protected $database;

    protected $tableName;

    public function __construct()
    {
        $this->tableName = self::TABLE_NAME;
        $this->database = DataBase::getInstance();
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function alreadyShared(int $owner_id, int $shared_user_id) : bool
    {
        $stmp = $this->database->getConnect()->prepare('SELECT * from ' . $this->getTableName() . ' where owner_id=:owner_id and shared_user_id=:shared_user_id');

        $stmp->bindParam('owner_id', $owner_id);
        $stmp->bindParam('shared_user_id', $shared_user_id);
        $stmp->execute();

        $result = $stmp->fetch();

        if (!$result)
        {
            return false;
        }

        return true;
    }
    
    public function existsHash(string $hash): bool
    {
        $stmp = $this->database->getConnect()->prepare('SELECT * from ' . $this->getTableName() . ' where hash=:hash');

        $stmp->bindParam(':hash', $hash);
        $stmp->execute();

        $result = $stmp->fetch();

        if (!$result)
        {
            return false;
        }

        return true;
    }

    public function getHashByUserId(int $user_id): ?string
    {
        $stmp = $this->database->getConnect()->prepare('SELECT * from ' . $this->getTableName() . ' where user_id=:user_id');

        $stmp->bindParam(':user_id', $user_id);
        $stmp->execute();
        $result = $stmp->fetch();

        return $result['hash'];
    }

    public function getUserIdByHash(string $hash) : int
    {
        $stmp = $this->database->getConnect()->prepare('SELECT * from ' . $this->getTableName() . ' where hash=:hash');

        $stmp->bindParam(':hash', $hash);
        $stmp->execute();
        $result = $stmp->fetch();

        return $result['user_id'];
    }

    public function insert(array $params): int
    {
        if (empty($params))
        {
            return 0;
        }

        $columns = [];
        $values = [];

        $insertTime = time();
        $hash = (string) md5(uniqid(rand(), true));

        if ( ! isset($params['owner_id']))
        {
            return 0;
        }

        $columns[] = 'owner_id';
        $values[] = $params['owner_id'];

        if ( ! isset($params['shared_user_id']))
        {
            return 0;
        }

        $columns[] = 'shared_user_id';
        $values[] = $params['shared_user_id'];

        if (isset($params['hash']))
        {
            $hash = $params['hash'];
        }

        $columns[] = 'hash';
        $values[] = $hash;

        $columns[] = 'mode';

        if ( ! isset($params['mode']))
        {
            $values[] = SharedTask::READ_MODE;
        }
        else
        {
            $values[] = $params['mode'];
        }

        if (empty($columns))
        {
            return 0;
        }

        $columns[] = 'updated_at';
        $values[] = $insertTime;
        
        $columns[] = 'created_at';
        $values[] = $insertTime;

        error_log('count: ' . count($columns));
 
        $columns = implode(',', $columns);
        
        $stmp = $this->database->getConnect()->prepare('insert into ' . $this->getTableName() . ' (' . $columns . ') values (?, ?, ?, ?, ?, ?)');

        $stmp->execute($values);
        
        error_log($this->database->getConnect()->lastInsertId());

        return $this->database->getConnect()->lastInsertId();
    }
    
    public function getSharedTasks(array $params) : array
    {
        $result = [];

        if (empty($params))
        {
            return $result;
        }

        $whereConditions = [];
        $whereParams = [];
        
        if (isset($params['id']))
        {
            $whereConditions[] = 'id = ?';
            $whereParams[] = $params['id'];
        }

        if (isset($params['owner_id']))
        {
            $whereConditions[] = 'owner_id = ?';
            $whereParams[] = $params['owner_id'];
        }
        
        if (isset($params['shared_user_id']))
        {
            $whereConditions[] = 'shared_user_id = ?';
            $whereParams[] = $params['shared_user_id'];
        }

        if (isset($params['hash']))
        {
            $whereConditions[] = 'hash = ?';
            $whereParams[] = $params['hash'];
        }

        if (empty($whereConditions))
        {
            return $result;
        }

        $where = implode('AND', $whereConditions);

        $stmp = $this->database->getConnect()->prepare('SELECT * from ' . $this->getTableName() . ' where ' . $where);

        $stmp->execute($whereParams);
        $sharedTasks = $stmp->fetchAll();
        
        foreach ($sharedTasks as $sharedTask) 
        {
            $result[] = new SharedTask($sharedTask);
        }
        
        return $result;
    }
    
    public function update(SharedTask $sharedTask) 
    {
        $updateTime = time();
        $stmp = $this->database->getConnect()->prepare('update ' . $this->getTableName() . ' set mode=:mode, updated_at=:updated_at where id=:id');

        $stmp->bindParam(':id', $sharedTask->getId());
        $stmp->bindParam(':mode', $sharedTask->getMode());
        $stmp->bindParam(':updated_at', $updateTime);

        $stmp->execute();
    }
    
    public function deleteSharedUserById(int $id)
    {
        $stmp = $this->database->getConnect()->prepare('delete from ' . $this->getTableName() . ' where id=:id');

        $stmp->bindParam(':id', $id);

        $stmp->execute();
    }
}

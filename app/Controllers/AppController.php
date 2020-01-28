<?php

namespace App\Controllers;

use App\Repositories\TaskRepository;
use App\Repositories\SharedTaskRepository;
use App\Repositories\UserRepository;

use App\Models\SharedTask;
use TodoApp\View;
use TodoApp\Auth;
use TodoApp\Router;
use TodoApp\Response;

class AppController 
{
    protected $redirectTo = '/auth/register';
    
    private $app;

    protected $auth;

    protected $router;

    protected $taskRepository;
    
    protected $sharedTaskRepository;
    
    protected $userRepository;

    protected $cache;

    public function __construct($app) 
    {
        $this->app = $app;

        $this->auth = Auth::getInstance();

        $this->router = new Router($_SERVER['REQUEST_URI']);

        if (!$this->auth->check())
        {
            $this->router->redirect($this->redirectTo);
        }

        $this->taskRepository = new TaskRepository;
        
        $this->sharedTaskRepository = new SharedTaskRepository;
        
        $this->userRepository = new UserRepository;
    }
    
    public function index() 
    {
        $tasklist = $this->app->cache()->get('todo.tasklist.' . $this->app->user()->getId());
        if (!$tasklist)
        {
            $tasklist = $this->taskRepository->getUserList($this->app->user()->getId());
            $this->app->cache()->set('todo.tasklist.' . $this->app->user()->getId(), $tasklist, 0, 3600);
        }

        $sharedTasksForUser = $this->sharedTaskRepository->getSharedTasks(['shared_user_id' => $this->app->user()->getId()]);
        
        $sharedUsers = $this->sharedTaskRepository->getSharedTasks(['owner_id' => $this->app->user()->getId()]);

        $params['shared_tasks_for_user'] = $sharedTasksForUser;
        $params['shared_users'] = $sharedUsers;
        $params['list'] = $tasklist;

        View::render('index', $params);
    }

    public function closeCompletedTasks(array $ids)
    {
        $result = [];

        if (empty($ids))
        {
            $response = new Response();
            $response->json(['result' => 'success', 'ids' => $result]);
        }

        $hasEditedPermission = false;

        foreach ($ids as $id)
        {
            $task = $this->taskRepository->getTask(['id' => $id])[0];

            if ($this->sharedTaskRepository->alreadyShared($task->getUserId(), $this->app->user()->getId()))
            {
                $sharedTask = $this->sharedTaskRepository->getSharedTasks(['user_id' => $task->getUserId(), 'shared_user_id' => $this->app->user()->getId()])[0];
                if ($sharedTask->getMode() == SharedTask::WRITE_MODE)
                {
                    $hasEditedPermission = true;
                }
            }
            else if($task->getUserId() == $this->app->user()->getId())
            {
                $hasEditedPermission = true;
            }

            if ($hasEditedPermission)
            {
                $this->app->cache()->delete('todo.tasklist.' . $task->getUserId());
                $this->taskRepository->deleteTask($id, $task->getUserId());

                $result[] = $id;
            }

            $hasEditedPermission = false;
        }

        $response = new Response();
        $response->json(['result' => 'success', 'ids' => $result]);
    }

    public function deleteTask(int $id, string $tasklistHash = null) 
    {
        $result = 0;
        $hasEditedPermission = false;

        $user = $this->app->user();
        $userId = $user->getId();
        
        $task = $this->taskRepository->getTask(['id' => $id])[0];

        if ($tasklistHash)
        {
            $sharedTask = $this->sharedTaskRepository->getSharedTasks(['hash' => $tasklistHash])[0];
            if ($sharedTask->getMode() == SharedTask::WRITE_MODE)
            {
                $userId = $sharedTask->getOwnerUser()->getId();
                $hasEditedPermission = true;
            }
        }
        else if ($task->getUserId() == $this->app->user()->getId())
        {
            $hasEditedPermission = true;
        }

        if ($hasEditedPermission)
        {
            $this->taskRepository->deleteTask($id, $userId);
            $this->app->cache()->delete('todo.tasklist.' . $userId);
            
            $result = $id;
        }

        $response = new Response();
        $response->json(['result' => 'success', 'task_id' => $result]);
    }

    public function addTask(string $name, string $tasklistHash = null) 
    {
        $hasEditedPermission = false;

        $user = $this->app->user();
        $userId = $user->getId();
        
        if ($tasklistHash)
        {
            $sharedTask = $this->sharedTaskRepository->getSharedTasks(['hash' => $tasklistHash])[0];
            if ($sharedTask->getMode() == SharedTask::WRITE_MODE)
            {
                $userId = $sharedTask->getOwnerUser()->getId();
                $hasEditedPermission = true;
            }
        } 
        else if ($task->getUserId() == $this->app->user()->getId())
        {
            $hasEditedPermission = true;
        }
        
        if ( ! $hasEditedPermission)
        {
            exit;
        }

        $id = $this->taskRepository->insert($_REQUEST['name'], $userId);
        $params['task'] = $this->taskRepository->getTask(['id' => $id])[0];

        $this->app->cache()->delete('todo.tasklist.' . $userId);
        
        $view = new View();
        $view->render('layouts/task', $params);
    }

    public function updateTask(int $id, string $name)
    {
        $response = new Response();
        $hasEditedPermission = false;

        $task = $this->taskRepository->getTask(['id' => $id])[0];

        if ($this->sharedTaskRepository->alreadyShared($task->getUserId(), $this->app->user()->getId()))
        {
            $sharedTask = $this->sharedTaskRepository->getSharedTasks(['user_id' => $task->getUserId(), 'shared_user_id' => $this->app->user()->getId()])[0];
            if ($sharedTask->getMode() == SharedTask::WRITE_MODE)
            {
                $hasEditedPermission = true;
            }
        } 
        else if ($task->getUserId() == $this->app->user()->getId())
        {
            $hasEditedPermission = true;
        }

        if ( ! $hasEditedPermission)
        {
            $response->json(['result' => 'success', 'task_id' => $id, 'name' => $task->getName()]);
            exit;
        }

        $task->setName($name);
        $this->taskRepository->updateTask($task);

        $this->app->cache()->delete('todo.tasklist.' . $task->getUserId());

        
        $response->json(['result' => 'success', 'task_id' => $id, 'name' => $name]);
    }

    public function openAllTasks(string $tasklistHash = null) 
    {
        $response = new Response();
        $hasEditedPermission = true;

        $user = $this->app->user();
        $userId = $user->getId();

        if ($tasklistHash)
        {
            $sharedTask = $this->sharedTaskRepository->getSharedTasks(['hash' => $tasklistHash])[0];
            if ($sharedTask->getMode() != SharedTask::WRITE_MODE)
            {
                $hasEditedPermission = false;
            }
            else 
            {
                $userId = $sharedTask->getOwnerUser()->getId();
            }
        }

        if ( ! $hasEditedPermission)
        {
            $response->json(['result' => 'fail']);
            exit;
        }

        $this->taskRepository->openAll($userId);
        $this->app->cache()->delete('todo.tasklist.' . $userId);

        $response->json(['result' => 'success']);
    }
    
    public function closeAllTasks(string $tasklistHash = null) 
    {
        $response = new Response();
        $hasEditedPermission = true;

        $user = $this->app->user();
        $userId = $user->getId();

        if ($tasklistHash)
        {
            $sharedTask = $this->sharedTaskRepository->getSharedTasks(['hash' => $tasklistHash])[0];
            if ($sharedTask->getMode() != SharedTask::WRITE_MODE)
            {
                $hasEditedPermission = false;
            } else
            {
                $userId = $sharedTask->getOwnerUser()->getId();
            }
        }

        if ( ! $hasEditedPermission)
        {
            $response->json(['result' => 'fail']);
            exit;
        }

        $this->taskRepository->closeAll($userId);
        $this->app->cache()->delete('todo.tasklist.' . $userId);

        $response->json(['result' => 'success']);
    }

    public function openTask(int $id) 
    {
        $response = new Response();
        $hasEditedPermission = false;

        $task = $this->taskRepository->getTask(['id' => $id])[0];

        if ($this->sharedTaskRepository->alreadyShared($task->getUserId(), $this->app->user()->getId()))
        {
            $sharedTask = $this->sharedTaskRepository->getSharedTasks(['user_id' => $task->getUserId(), 'shared_user_id' => $this->app->user()->getId()])[0];
            if ($sharedTask->getMode() == SharedTask::WRITE_MODE)
            {
                $hasEditedPermission = true;
            }
        } 
        else if ($task->getUserId() == $this->app->user()->getId())
        {
            $hasEditedPermission = true;
        }

        if ( ! $hasEditedPermission)
        {
            $response->json(['result' => 'fail']);
            exit;
        }

        $task->open();
        $this->taskRepository->updateTask($task);

        $this->app->cache()->delete('todo.tasklist.' . $task->getUserId());


        $response->json(['result' => 'success', 'task_id' => $id]);
    }

    public function closeTask(int $id)
    {
        $response = new Response();
        $hasEditedPermission = false;

        $task = $this->taskRepository->getTask(['id' => $id])[0];

        if ($this->sharedTaskRepository->alreadyShared($task->getUserId(), $this->app->user()->getId()))
        {
            $sharedTask = $this->sharedTaskRepository->getSharedTasks(['user_id' => $task->getUserId(), 'shared_user_id' => $this->app->user()->getId()])[0];
            if ($sharedTask->getMode() == SharedTask::WRITE_MODE)
            {
                $hasEditedPermission = true;
            }
        } 
        else if ($task->getUserId() == $this->app->user()->getId())
        {
            $hasEditedPermission = true;
        }

        if ( ! $hasEditedPermission)
        {
            $response->json(['result' => 'fail']);
            exit;
        }

        $task->close();
        $this->taskRepository->updateTask($task);

        $this->app->cache()->delete('todo.tasklist.' . $task->getUserId());


        $response->json(['result' => 'success', 'task_id' => $id]);
    }

    public function viewSharedTasks(string $tasklistHash)
    {
        if ( ! $this->sharedTaskRepository->existsHash($tasklistHash))
        {
            $this->router->redirect($this->redirectTo);
        }

        $sharedTask = $this->sharedTaskRepository->getSharedTasks(['hash' => $tasklistHash])[0];
        $tasklist = $this->taskRepository->getUserList($sharedTask->getOwnerUser()->getId());
        
        $params['list'] = $tasklist;
        $params['tasklist_hash'] = $tasklistHash;

        View::render('index', $params);
    }
    
    public function addSharedUser(string $email, string $rules = null)
    {
        $sharedUser = $this->userRepository->getUserByEmail($email);
        if ( ! $sharedUser)
        {
            $this->router->redirect($this->redirectTo);
        }

        $user = $this->app->user();
        if ($user->getId() == $sharedUser->getId())
        {
            $this->router->redirect($this->redirectTo);
        }

        if ($this->sharedTaskRepository->alreadyShared($user->getId(), $sharedUser->getId()))
        {
            $this->router->redirect($this->redirectTo);
        }

        $params['owner_id'] = $user->getId();
        $params['shared_user_id'] = $sharedUser->getId();
        $params['mode'] = $rules;

        $this->sharedTaskRepository->insert($params);
        
        $this->router->redirect($this->redirectTo);
    }
    
    public function toggleMode(int $sharedTaskId)
    {
        $sharedTask = $this->sharedTaskRepository->getSharedTasks(['id' => $sharedTaskId])[0];
        $sharedTask->toggleMode();
        $this->sharedTaskRepository->update($sharedTask);

        $response = new Response();
        $response->json(['result' => 'success']);
    }

    public function deleteSharedUser(int $sharedUserTaskId)
    {
        $this->sharedTaskRepository->deleteSharedUserById($sharedUserTaskId);

        $response = new Response();
        $response->json(['result' => 'success', 'id' => $sharedUserTaskId]);
    }
}

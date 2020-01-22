<?php

namespace App\Controllers;

use App\Repositories\TaskRepositories;

use TodoApp\View;
use TodoApp\Auth;
use TodoApp\Router;
use TodoApp\Response;

class AppController 
{
    protected $redirectTo = '/auth/register';
    
    protected $auth;

    protected $router;

    protected $taskRepository;
    
    public function __construct() 
    {
        $this->auth = Auth::getInstance();

        $this->router = new Router($_SERVER['REQUEST_URI']);

        if (!$this->auth->check())
        {
            $this->router->redirect($this->redirectTo);
        }

        $this->taskRepository = new TaskRepositories;
    }

    public function index() 
    {
        $params['list'] = $this->taskRepository->getUserList($this->auth->user()['id']);
        View::render('index', $params);
    }
    
    public function closeCompletedTasks(array $ids)
    {
        foreach($ids as $id)
        {
            $task = $this->taskRepository->deleteTask($id, $this->auth->user()['id']);
        }

        $response = new Response();
        $response->json(['result' => 'success', 'ids' => $ids]);
    }

    public function deleteTask(int $id) 
    {
        $this->taskRepository->deleteTask($id, $this->auth->user()['id']);
        
        $response = new Response();
        $response->json(['result' => 'success', 'task_id' => $id]);
    }

    public function addTask() 
    {
        $id = $this->taskRepository->insert($_REQUEST['name'], $this->auth->user()['id']);
        $params['task'] = $this->taskRepository->getTask($id, $this->auth->user()['id']);

        $view = new View();
        $view->render('layouts/task', $params);
    }

    public function updateTask(int $id, string $name)
    {
        $task = $this->taskRepository->getTask($id, $this->auth->user()['id']);
        $task['name'] = $name;
        $task = $this->taskRepository->updateTask($task);
        
        $response = new Response();
        $response->json(['result' => 'success', 'task_id' => $id, 'name' => $name]);
    }

    public function openAllTasks() 
    {
        $task = $this->taskRepository->openAll();
        
        $response = new Response();
        $response->json(['result' => 'success']);
    }
    
    public function closeAllTasks() 
    {
        $task = $this->taskRepository->closeAll();
        
        $response = new Response();
        $response->json(['result' => 'success']);
    }

    public function openTask(int $id) 
    {
        $task = $this->taskRepository->getTask($id, $this->auth->user()['id']);
        $task['status'] = '1';
        $task = $this->taskRepository->updateTask($task);
        
        $response = new Response();
        $response->json(['result' => 'success', 'task_id' => $id]);
    }

    public function closeTask(int $id)
    {
        $task = $this->taskRepository->getTask($id, $this->auth->user()['id']);
        $task['status'] = '0';
        $task = $this->taskRepository->updateTask($task);

        $response = new Response();
        $response->json(['result' => 'success', 'task_id' => $id]);
    }
}

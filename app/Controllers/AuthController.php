<?php

namespace App\Controllers;

use App\Repositories\UserRepository;

use TodoApp\SessionErrorContainer;
use TodoApp\View;
use TodoApp\Router;
use TodoApp\Guard;
use TodoApp\Auth;

class AuthController 
{
    protected $redirectTo = '/';
    
    protected $auth;

    protected $router;
    
    protected $userRepositories;

    protected $errorsContainer;

    public function __construct()
    {
        $this->router = new Router($_SERVER['REQUEST_URI']);
        $this->userRepositories = new UserRepository();
        $this->errorsContainer = SessionErrorContainer::getInstance();
        $this->auth = Auth::getInstance();
    }

    public function loginPage() 
    {
        if ($this->auth->check()) 
        {
            $this->router->redirect($this->redirectTo);
        }

        View::render('auth/login', [], $this->errorsContainer->getAll());
    }

    public function registerPage() 
    {
        if($this->auth->check())
        {
            $this->router->redirect($this->redirectTo);
        }

        View::render('auth/register', [], $this->errorsContainer->getAll());
    }
    
    public function login(string $email, string $password) 
    {
        if (empty($email) || empty($password)) 
        {
            $this->errorsContainer->push('all_fields', 'Please fill all fields');
            $this->router->redirect('/auth/login');
        }

        $user = $this->userRepositories->getUserByEmail($email);
        if (!$user) 
        {
            $this->errorsContainer->push('user_not_exists', 'Wrong username or password combination');
            $this->router->redirect('/auth/login');
        }

        $guard = new Guard();
        $hashedPassword = $guard->crypt($password);

        if ($user->getPassword() != $hashedPassword)
        {
            $this->errorsContainer->push('user_not_exists', 'Wrong username or password combination');
            $this->router->redirect('/auth/login');
        }

        $this->auth->login($email);

        $this->router->redirect('/');
    }

    public function register(string $name, string $email, string $password, string $confirm_password)
    {
        if (empty($name) || empty($email) || empty($password) || empty($confirm_password) )
        {
            $this->errorsContainer->push('all_fields', 'Please fill all fields');
            $this->router->redirect('/auth/register');
        }

        if (strlen($password) < 6) 
        {
            $this->errorsContainer->push('password', 'Password cannot be less than 6 characters');
            $this->router->redirect('/auth/register');
        }

        if ($password != $confirm_password)
        {
            $this->errorsContainer->push('password', 'The two passwords do not match');
            $this->router->redirect('/auth/register');
        }

        if ($this->userRepositories->existsUser($email))
        {
            $this->errorsContainer->push('user_already_exists', 'User already exist');
            $this->router->redirect('/auth/register');
        }

        $guard = new Guard();
        $hashedPassword = $guard->crypt($password);

        $user = $this->userRepositories->createUser($name, $email, $hashedPassword);

        if (!$user)
        {
            $this->errorsContainer->push('user_creation_error', 'Error with account creation');
            $this->router->redirect('/auth/register');
        }

        $this->auth->login($email);

        $this->router->redirect('/');
    }
    
    public function logout() 
    {
        $this->auth->logout();
        $this->router->redirect('/');
    }
}

<?php

namespace TodoApp;

class Application 
{
    protected $configsPath;

    protected $viewsPath;

    protected $router;
    
    protected $controllerResolver;

    public function __construct()
    {
        $this->loadConfigFiles();

        $this->router = new Router($_SERVER['REQUEST_URI']);

        $this->controllerResolver = new ControllerResolver();
    }

    protected function loadConfigFiles() 
    {
        require_once __DIR__ . "/../../config/app.php";
        require_once __DIR__ . "/../../config/database.php";
    }

    public function handleRequest() 
    {
        $routeHandler = $this->router->getRouteHandler();
        $controller = $this->controllerResolver->getController($routeHandler);
    }

    public function getUser() 
    {
        
    }
}

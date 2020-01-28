<?php

namespace TodoApp;

class Router 
{
    protected $currentUri;

    protected $routes;

    public function __construct(string $currentUri) 
    {
        $this->currentUri = $currentUri;
        $this->loadConfigFiles();
    }

    protected function loadConfigFiles() 
    {
        $this->routes = include(BASE_DIR . "config/routes.php");
    }

    public function redirect(string $uri)
    {
        header("Location: " . $uri);
        exit();
    }

    public function getRouteHandler() : string
    {
        $routeKey = strtok($this->currentUri, '?');

        $start = strpos($routeKey, 'index.php');
        if ($start != false)
        {
            $routeKey = substr($routeKey, 0, $start);
        }

        if (!key_exists($routeKey, $this->routes))
        {
            http_response_code(404);
        }
        
        return $this->routes[$routeKey];
    }
}

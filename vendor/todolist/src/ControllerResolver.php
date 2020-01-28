<?php

namespace TodoApp;

class ControllerResolver 
{
    public function getController(string $routeHandler, $app)
    {
        $controller = explode('@', $routeHandler);
        $controller[0] = $this->instantiateController($controller[0], $app);
        call_user_func_array($controller, $_REQUEST);
    }

    protected function instantiateController($class, $app) 
    {
        $className = 'App\Controllers\\' . $class;
        return new $className($app);
    }
}

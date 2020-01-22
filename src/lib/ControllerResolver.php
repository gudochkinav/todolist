<?php

namespace TodoApp;

class ControllerResolver 
{
    public function getController(string $routeHandler)
    {
        $controller = explode('@', $routeHandler);
        $controller[0] = $this->instantiateController($controller[0]);
        call_user_func_array($controller, $_REQUEST);
    }

    protected function instantiateController($class) 
    {
        $className = 'App\Controllers\\' . $class;
        return new $className();
    }
}

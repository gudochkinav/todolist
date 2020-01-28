<?php

namespace TodoApp;

use TodoApp\Cache\Repository as CacheRepository;
use TodoApp\Config\Repository as ConfigRepository;
use TodoApp\Cache\Cache;
use TodoApp\Config\Config;
use TodoApp\Auth;

class Application 
{
    protected $basePath;

    protected $router;
    
    protected $controllerResolver;
    
    protected $cache;
    
    protected $config;
    
    protected $auth;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;

        $this->loadConfigFiles();

        $this->router = new Router($_SERVER['REQUEST_URI']);

        $this->controllerResolver = new ControllerResolver();
        
        $this->auth = Auth::getInstance();
    }

    protected function loadConfigFiles() 
    {
        $appConfig['app'] = (array) include($this->basePath . "config/app.php");
        $databaseConfig['database'] = (array) include($this->basePath . "config/database.php");
        $routeConfig['route'] = (array) include($this->basePath . "config/routes.php");
        $cacheConfig['cache'] = (array) include($this->basePath . "config/cache.php");
        $config = array_merge($appConfig, $cacheConfig, $databaseConfig, $routeConfig);

        $this->config = new Config($config);
    }

    public function handleRequest() 
    {
        $routeHandler = $this->router->getRouteHandler();
        $controller = $this->controllerResolver->getController($routeHandler, $this);
    }

    public function cache() : CacheRepository
    {
        if (!$this->cache)
        {
            if ($this->config->get('cache.driver') == 'memcache')
            {
                $this->cache = new Cache($this->config->get('cache.memcache.host'), (int) $this->config->get('cache.memcache.port'));
            }
        }

        return $this->cache;
    }
    
    public function config() : ConfigRepository
    {
        return $this->config;
    }

    public function user()
    {
        return $this->auth->user();
    }
}

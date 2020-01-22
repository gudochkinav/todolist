<?php

namespace TodoApp;

class View
{
    private const DEFAULT_NAME = 'index';

    private const DEFAULT_PATH = ROOT_DIRECTORY.'resources/views/';
    
    private const DEFAULT_EXTENSION = '.php';
    
    public static function render(string $templateName, array $params = [], array $errors = [])
    {
        $params['errors'] = $errors;

        if (!empty($params))
        {
            extract($params);
        }

        include(self::DEFAULT_PATH . $templateName . self::DEFAULT_EXTENSION);
    }
}

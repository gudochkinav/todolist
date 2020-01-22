<?php

return [
    '/'                => 'AppController@index',

    '/add-task'              => 'AppController@addTask',
    '/close-task'            => 'AppController@closeTask',
    '/open-task'             => 'AppController@openTask',
    '/close-all-tasks'       => 'AppController@closeAllTasks',
    '/open-all-tasks'        => 'AppController@openAllTasks',
    '/delete-task'           => 'AppController@deleteTask',
    '/update-task'           => 'AppController@updateTask',
    '/close-completed-tasks' => 'AppController@closeCompletedTasks',
    
    '/auth/register'   => 'AuthController@registerPage',
    '/auth/login'      => 'AuthController@loginPage',
    '/auth/logout'     => 'AuthController@logout',

    '/auth/register/submit'   => 'AuthController@register',
    '/auth/login/submit'      => 'AuthController@login'
];

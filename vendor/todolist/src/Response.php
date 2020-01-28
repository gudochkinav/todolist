<?php

namespace TodoApp;

class Response 
{
    public function json(array $data) 
    {
        header('Content-type: application/json;charset=utf-8');
        echo json_encode($data);
    }
}
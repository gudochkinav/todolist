<?php
session_start();

include __DIR__.'/../vendor/autoload.php';

ini_set("log_errors", 1);
ini_set("error_log", __DIR__."/../logs/app.log");

use TodoApp\Application;

$app = new Application();
$app->handleRequest();

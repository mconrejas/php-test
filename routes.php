<?php

use App\Core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/search', 'HomeController@search');

// Dispatch routes
$router->dispatch();

<?php

$router->get('/', 'UserController@index');
$router->get('/users', 'UserController@index');

$router->get('/users/create', 'UserController@create');
$router->post('/users/store', 'UserController@store');

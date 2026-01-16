<?php

$router->get('/', 'AuthController@home');

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

$router->get('/users', 'UserController@index');
$router->get('/users/create', 'UserController@create');
$router->post('/users/store', 'UserController@store');
$router->get('/users/edit', 'UserController@edit');
$router->post('/users/update', 'UserController@update');
$router->post('/users/delete', 'UserController@delete');

<?php

// Public
$router->get('/', 'AuthController@home');

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');

$router->get('/dashboard', 'DashboardController@index');
$router->get('/dashboard/coach', 'DashboardController@coach');
$router->get('/dashboard/sportif', 'DashboardController@sportif');

$router->post('/seances/store', 'SeanceController@store');
$router->post('/seances/delete', 'SeanceController@delete');

$router->post('/reservations/reserve', 'ReservationController@reserve');
$router->post('/reservations/cancel', 'ReservationController@cancel');

$router->get('/users', 'UserController@index');
$router->get('/users/create', 'UserController@create');
$router->post('/users/store', 'UserController@store');
$router->get('/users/edit', 'UserController@edit');
$router->post('/users/update', 'UserController@update');
$router->post('/users/delete', 'UserController@delete');

$router->post('/coach/profile', 'CoachController@updateProfile');

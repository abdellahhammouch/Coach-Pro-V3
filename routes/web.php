<?php

// Home (redirige selon rôle)
$router->get('/', 'DashboardController@index');

// Auth
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');

// Dashboards
$router->get('/dashboard', 'DashboardController@index');
$router->get('/dashboard/coach', 'DashboardController@coach');
$router->get('/dashboard/sportif', 'DashboardController@sportif');

// Users CRUD
$router->get('/users', 'UserController@index');
$router->get('/users/create', 'UserController@create');
$router->post('/users/store', 'UserController@store');
$router->get('/users/edit', 'UserController@edit');
$router->post('/users/update', 'UserController@update');
$router->post('/users/delete', 'UserController@delete');

// Coach actions (séances)
$router->post('/seances/store', 'SeanceController@store');
$router->post('/seances/delete', 'SeanceController@delete');

// Sportif actions (réservations)
$router->post('/reservations/reserve', 'ReservationController@reserve');
$router->post('/reservations/cancel', 'ReservationController@cancel');

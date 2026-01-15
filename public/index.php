<?php

require __DIR__ . '/../vendor/autoload.php';

session_start();

require __DIR__ . '/../core/helpers.php';

$router = new Router();
require __DIR__ . '/../routes/web.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

?>
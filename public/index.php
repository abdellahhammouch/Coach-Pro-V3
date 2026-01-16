<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
session_start();

require __DIR__ . '/../core/helpers.php';

spl_autoload_register(function (string $class): void {
    $paths = [
        __DIR__ . '/../core/' . $class . '.php',
        __DIR__ . '/../app/Controllers/' . $class . '.php',
        __DIR__ . '/../app/Repositories/' . $class . '.php',
        __DIR__ . '/../app/Models/' . $class . '.php',
    ];

    foreach ($paths as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

$router = new Router();
require __DIR__ . '/../routes/web.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

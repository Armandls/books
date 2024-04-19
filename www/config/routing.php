<?php
declare(strict_types=1);

// Rutas

use Project\Bookworm\Middleware\SessionMiddleware;
use Project\Bookworm\Controller\LandingController;

$app->add(SessionMiddleware::class);

// 1- Cuando me llegue una petición GET a la ruta /, se ejecutará el método apply de la clase HomeController
$app->get('/', LandingController::class . ':apply')->setName('home');

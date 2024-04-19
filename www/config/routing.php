<?php
declare(strict_types=1);

// Rutas

use Project\Bookworm\Controller\SignInController;
use Project\Bookworm\Middleware\SessionMiddleware;
use Project\Bookworm\Controller\LandingController;

$app->add(SessionMiddleware::class);

// 1- Cuando me llegue una petición GET a la ruta /, se ejecutará el método apply de la clase HomeController
$app->get('/', LandingController::class . ':apply')->setName('home');

// 4- Cuando me llegue una petición GET a la ruta /sign-in, se ejecutará el método showForm de la clase SignInController
$app->get('/sign-in', SignInController::class . ':showForm')->setName('get-login');
// 5- Cuando me llegue una petición POST a la ruta /sign-in, se ejecutará el método handleFormSubmission de la clase SignInController
//$app->post('/sign-in', SignInController::class . ':handleFormSubmission')->setName('login-form');

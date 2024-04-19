<?php
declare(strict_types=1);  // strict type declaration version support from PHP 7.0, booleano que dice que debe ser estricto en el tipado de datos

use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// THIS ORDER IS IMPORTANT -> FIRST DEPENDENCIES, THEN ROUTING
require_once __DIR__ . '/../config/dependencies.php'; // Redirige a dependencies.php y crea el contenedor

AppFactory::setContainer($container); // Set container to AppFactory

$app = AppFactory::create(); // Create App with Slim Factory


// Middleware -> se ejecuta despues de la aplicación
$app->add(TwigMiddleware::createFromContainer($app));
// Middleware -> se ejecuta antes de la aplicación
$app->addBodyParsingMiddleware();
// Middleware -> se ejecuta antes de la aplicación
$app->addRoutingMiddleware(); // Add routing middleware
// Middleware -> errores de la aplicación, se ejecuta tanto antes como despues de la aplicación
$app->addErrorMiddleware(true, false, false);


require_once __DIR__ . '/../config/routing.php'; // Include routing file

$app->run();
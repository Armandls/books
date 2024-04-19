<?php
// Este archivo se encarga de las dependencias de la aplicación
// Cada clase que se instancie en la aplicación debe estar en este archivo

declare(strict_types=1);

use DI\Container;
use Psr\Container\ContainerInterface;
use Project\Bookworm\Controller\LandingController;
use Slim\Views\Twig;

$container = new Container(); // Instancia de la clase Container

// IMPORTA EL ORDEN DE LAS DEPENDENCIAS

//EXTERNAL DEPENDENCIES
// 1- Se añade la instancia de la clase Container al contenedor de Slim
$container->set(
    'view',     // Nombre de la dependencia -> view (Twig)
    function () {
        return Twig::create(__DIR__ . '/../templates', ['cache' => false]);  // Constructor (path, array de configuración)
    }
);



//PDO + MODELS



//CONTROLLERS
// 1- Se añade la instancia de la clase LandingController al contenedor de Slim
$container->set(
    LandingController::class,  // Nombre de la dependencia -> LandingController
    function (ContainerInterface $c) {
        $controller = new LandingController($c->get("view"));  // Constructor (Twig)
        return $controller;
    }
);





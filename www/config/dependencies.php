<?php
// Este archivo se encarga de las dependencias de la aplicación
// Cada clase que se instancie en la aplicación debe estar en este archivo

declare(strict_types=1);

use DI\Container;
use Project\Bookworm\Controller\SignInController;
use Project\Bookworm\Controller\SignUpController;
use Psr\Container\ContainerInterface;
use Project\Bookworm\Controller\LandingController;
use Project\Bookworm\Model\Repository\MySQLUserRepository;
use Project\Bookworm\Model\Repository\PDOSingleton;
use Project\Bookworm\Model\UserRepository;
use Slim\Flash\Messages;
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

        // 2- Se añade la instancia de la clase Messages al contenedor de Slim
            $container->set('flash',  function () {
                return new Messages();
            });



    //PDO + MODELS
        // 1- Se añade la instancia de la clase PDOSingleton al contenedor de Slim
            $container->set('db', function () {
                return PDOSingleton::getInstance(
                    $_ENV['MYSQL_USER'],
                    $_ENV['MYSQL_PASSWORD'],
                    $_ENV['MYSQL_HOST'],
                    $_ENV['MYSQL_PORT'],
                    $_ENV['MYSQL_DATABASE']
                );
            });

        // 2- Se añade la instancia de la clase MySQLUserRepository al contenedor de Slim
            $container->set(UserRepository::class, function (ContainerInterface $container) {
                return new MySQLUserRepository($container->get('db'));
            });



    //CONTROLLERS
        // 1- Se añade la instancia de la clase LandingController al contenedor de Slim
            $container->set(
                LandingController::class,  // Nombre de la dependencia -> LandingController
                function (ContainerInterface $c) {
                    $controller = new LandingController($c->get("view"));  // Constructor (Twig)
                    return $controller;
                }
            );

        // 2- Se añade SignUpController al contenedor de Slim
            $container->set(
                SignUpController::class,  // Nombre de la dependencia -> CookieMonsterController
                function (ContainerInterface $c) {
                    // Constructor (Twig)
                    return new SignUpController($c->get("view"), $c->get(UserRepository::class));
                }
            );

        // 3- Se añade SignInController al contenedor de Slim
            $container->set(
                SignInController::class,  // Nombre de la dependencia -> CookieMonsterController
                function (ContainerInterface $c) {
                    // Constructor (Twig)
                    return new SignInController($c->get("view"), $c->get(UserRepository::class), $c->get("flash"));
                }
            );





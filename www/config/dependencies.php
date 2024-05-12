<?php
// Este archivo se encarga de las dependencias de la aplicación
// Cada clase que se instancie en la aplicación debe estar en este archivo

declare(strict_types=1);

use DI\Container;
use Project\Bookworm\Controller\ApiForumsController;
use Project\Bookworm\Controller\ApiPostsController;
use Project\Bookworm\Controller\BookDetailsController;
use Project\Bookworm\Controller\CatalogueController;
use Project\Bookworm\Controller\FlashController;
use Project\Bookworm\Controller\ForumsController;
use Project\Bookworm\Controller\PostsController;
use Project\Bookworm\Controller\SignInController;
use Project\Bookworm\Controller\SignUpController;
use Project\Bookworm\Controller\UserProfile;
use Project\Bookworm\Model\BookRepository;
use Project\Bookworm\Model\ForumsRepository;
use Project\Bookworm\Model\PostRepository;
use Project\Bookworm\Model\Repository\MySQLBookRepository;
use Project\Bookworm\Model\Repository\MySQLForumsRepository;
use Project\Bookworm\Model\Repository\MySQLPostRepository;
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

        // 3- Se añade la instancia de la clase MySQLBookRepository al contenedor de Slim
            $container->set(BookRepository::class, function (ContainerInterface $container) {
                return new MySQLBookRepository($container->get('db'));
            });
        // 4- Se añade la instancia de la clase MySQLForumsRepository al contenedor de Slim
        $container->set(ForumsRepository::class, function (ContainerInterface $container) {
            return new MySQLForumsRepository($container->get('db'));
        });

        // 5- Se añade la instancia de la clase MySQLPostsRepository al contenedor de Slim
        $container->set(PostRepository::class, function (ContainerInterface $container) {
            return new MySQLPostRepository($container->get('db'));
        });



    //CONTROLLERS

        // 1- Se añade FlashController al contenedor de Slim
        $container->set(
            FlashController::class,  // Nombre de la dependencia -> CookieMonsterController
            function (ContainerInterface $c) {
                // Constructor (Twig)
                return new FlashController($c->get("view"), $c->get("flash"));
            }
        );

        // 2- Se añade la instancia de la clase LandingController al contenedor de Slim
            $container->set(
                LandingController::class,  // Nombre de la dependencia -> LandingController
                function (ContainerInterface $c) {
                    $controller = new LandingController($c->get("view"), $c->get(UserRepository::class), $c->get(FlashController::class));  // Constructor (Twig)
                    return $controller;
                }
            );

        // 3- Se añade SignUpController al contenedor de Slim
            $container->set(
                SignUpController::class,  // Nombre de la dependencia -> CookieMonsterController
                function (ContainerInterface $c) {
                    // Constructor (Twig)
                    return new SignUpController($c->get("view"), $c->get(UserRepository::class));
                }
            );

        // 4- Se añade SignInController al contenedor de Slim
            $container->set(
                SignInController::class,  // Nombre de la dependencia -> CookieMonsterController
                function (ContainerInterface $c) {
                    // Constructor (Twig)
                    return new SignInController($c->get("view"), $c->get(UserRepository::class), $c->get("flash"));
                }
            );

        // 5- Se añade CatalogueController al contenedor de Slim
            $container->set(
                CatalogueController::class,  // Nombre de la dependencia -> CatalogueController
                function (ContainerInterface $c) {
                    // Constructor (Twig)
                    return new CatalogueController($c->get("view"), $c->get(BookRepository::class), $c->get(UserRepository::class),  $c->get(FlashController::class));
                }
            );

            // Se añade BookDetailsController al contenedor de Slim
            $container->set(
                BookDetailsController::class,
                function (ContainerInterface $c) {
                    // Constructor (Twig, BookRepository, Messages)
                    return new BookDetailsController($c->get("view"), $c->get(BookRepository::class), $c->get(UserRepository::class), $c->get(FlashController::class), $c->get("flash"));
                }
            );

        // 6- Se añade UserProfile al contenedor de Slim
            $container->set(
                UserProfile::class,  // Nombre de la dependencia -> UserProfile
                function (ContainerInterface $c) {
                    // Constructor (Twig)
                    return new UserProfile($c->get("view"), $c->get(UserRepository::class), $c->get(FlashController::class), $c->get("flash"));
                }
            );

        // 7- Se añade ForumsController al contenedor de Slim
        $container->set(
            ForumsController::class,  // Nombre de la dependencia -> ForumsController
            function (ContainerInterface $c) {
                // Constructor (Twig)
                return new ForumsController($c->get("view"), $c->get(ForumsRepository::class), $c->get(UserRepository::class),  $c->get(FlashController::class));
            }
        );

        // 8- Se añade PostsController al contenedor de Slim
        $container->set(
            PostsController::class,  // Nombre de la dependencia -> PostsController
            function (ContainerInterface $c) {
                // Constructor (Twig)
                return new PostsController($c->get("view"), $c->get(ForumsRepository::class), $c->get(PostRepository::class), $c->get(UserRepository::class),  $c->get(FlashController::class));
            }
        );

        // 9- Se añade ApiForumsController al contenedor de Slim
        $container->set(
            ApiForumsController::class,  // Nombre de la dependencia -> ApiForumsController
            function (ContainerInterface $c) {
                // Constructor (Twig)
                return new ApiForumsController($c->get("view"), $c->get(ForumsRepository::class), $c->get(UserRepository::class),  $c->get(FlashController::class));
            }
        );

        // 10 - Se añade ApiPostsController al contenedor de Slim
        $container->set(
            ApiPostsController::class,  // Nombre de la dependencia -> ApiPostsController
            function (ContainerInterface $c) {
                // Constructor (Twig)
                return new ApiPostsController($c->get("view"), $c->get(ForumsRepository::class), $c->get(UserRepository::class), $c->get(PostRepository::class),  $c->get(FlashController::class));
            }
        );









<?php
declare(strict_types=1);

use DI\Container;
use Psr\Container\ContainerInterface;

use Salle\LSCryptoNews\Controller\CookieMonsterController;
use Salle\LSCryptoNews\Controller\HomeController;

use Salle\LSCryptoNews\Controller\LoginController;
use Salle\LSCryptoNews\Controller\MarketUpdatesController;
use Salle\LSCryptoNews\Controller\NewsController;
use Salle\LSCryptoNews\Controller\RegisterController;

use Salle\LSCryptoNews\MiddleWare\SessionCheckerMiddleware;
use Salle\LSCryptoNews\Model\PDOSingleton;
use Salle\LSCryptoNews\Model\UserRepository;
use Salle\LSCryptoNews\Model\UserSQL;

use Slim\Views\Twig;
use Slim\Flash\Messages;

require_once __DIR__ . '/../vendor/autoload.php';


$container = new Container();
$container->set('flash', function () {
        return new Messages();
    }
);

$container->set('view', function () {
        return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
    }
);

$container->set(CookieMonsterController::class, function (ContainerInterface $c) {
    return new CookieMonsterController($c->get('view'));
});

$container->set(
    HomeController::class,
    function (ContainerInterface $c) {
        $controller = new HomeController($c->get("view"), $c->get("flash"));
        return $controller;
    }
);

$container->set('db', function () {
    return PDOSingleton::getInstance(
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_PASSWORD'],
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_PORT'],
        $_ENV['MYSQL_DATABASE']
    );
});

$container->set(UserRepository::class, function (ContainerInterface $container) {
    return new UserSQL($container->get('db'));
});

$container->set(
    RegisterController::class,
    function (ContainerInterface $c) {
        return new RegisterController($c->get("view"), $c->get(UserRepository::class), $c->get(Messages::class));
    }
);


$container->set(
    LoginController::class,
    function (ContainerInterface $c) {
        return new LoginController($c->get("view"), $c->get(UserRepository::class), $c->get("flash"));
    }
);


$container->set(
    NewsController::class,
    function (ContainerInterface $c) {
        return new NewsController($c->get("view"), $c->get("view"));
    }
);

$container->set(
    MarketUpdatesController::class,
    function (ContainerInterface $c) {
        return new MarketUpdatesController($c->get("view"));
    }
);


$container->set(SessionCheckerMiddleware::class, function (ContainerInterface $c) {
    return new SessionCheckerMiddleware($c->get(Messages::class));
});

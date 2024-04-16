<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Salle\LSCryptoNews\Controller\HomeController;
use Salle\LSCryptoNews\Controller\LoginController;
use Salle\LSCryptoNews\Controller\MarketUpdatesController;
use Salle\LSCryptoNews\Controller\NewsController;
use Salle\LSCryptoNews\Controller\RegisterController;

use Salle\LSCryptoNews\MiddleWare\SessionCheckerMiddleware;
use Salle\LSCryptoNews\MiddleWare\SessionMiddleWare;

//require_once __DIR__ . '/../vendor/autoload.php';
//require __DIR__ . '/../public/index.php';




$app->get('/', HomeController::class . ':apply')->setName('home');
$app->add(SessionMiddleware::class);

//$app->get('/cookies_advice', CookieMonsterController::class . ':showAdvice')->setName('cookies_advice');
//$app->add(FlashController::class);  // Then FlashController



$app->get('/sign-up', RegisterController::class . ':apply')->setName('sign-up');
$app->post('/sign-up', RegisterController::class . ':checkFormSubmit')->setName('sign-up');
//
$app->get('/sign-in', LoginController::class . ':apply')->setName('sign-in');
$app->post('/sign-in', LoginController::class . ':checkLoginSubmit')->setName('sign-in');

$app->get('/news', NewsController::class . ':apply')->setName('news')->add(SessionCheckerMiddleware::class);
$app->get('/mkt', MarketUpdatesController::class . ':apply')->setName('mkt');


//
<?php
declare(strict_types=1);

// Rutas

use Project\Bookworm\Controller\BookDetailsController;
use Project\Bookworm\Controller\CatalogueController;
use Project\Bookworm\Controller\ForumsController;
use Project\Bookworm\Controller\PostsController;
use Project\Bookworm\Controller\SignInController;
use Project\Bookworm\Controller\SignUpController;
use Project\Bookworm\Middleware\SessionCheckerMiddleware;
use Project\Bookworm\Controller\UserProfile;
use Project\Bookworm\Middleware\SessionMiddleware;
use Project\Bookworm\Controller\LandingController;

$app->add(SessionMiddleware::class);

// 1- Cuando me llegue una petición GET a la ruta /, se ejecutará el método apply de la clase HomeController
$app->get('/', LandingController::class . ':apply')->setName('home');

// 2- Cuando me llegue una petición GET a la ruta /sign-up, se ejecutará el método showForm de la clase SignUpController
$app->get('/sign-up', SignUpController::class . ':showForm')->setName('get-sign-up');
// 3- Cuando me llegue una petición POST a la ruta /sign-up, se ejecutará el método handleFormSubmission de la clase SignUpController
$app->post('/sign-up', SignUpController::class . ':handleFormSubmission')->setName('handle-form');

// 4- Cuando me llegue una petición GET a la ruta /sign-in, se ejecutará el método showForm de la clase SignInController
$app->get('/sign-in', SignInController::class . ':showForm')->setName('get-sign-in');
// 5- Cuando me llegue una petición POST a la ruta /sign-in, se ejecutará el método handleFormSubmission de la clase SignInController
$app->post('/sign-in', SignInController::class . ':handleFormSubmission')->setName('login-form');

// 6- Cuando me llegue una petición GET a la ruta /profile, se ejecutarán los métodos showProfile de la clase UserProfile
$app->get('/profile', UserProfile::class . ':showProfile')->setName('show-profile');
// 7- Cuando me llegue una petición POST a la ruta /profile, se ejecutarán los métodos editProfile de la clase UserProfile
$app->post('/profile', UserProfile::class . ':editProfile')->setName('edit-profile');

// 8- Cuando me llegue una petición GET a la ruta /catalogue, se ejecutará el método apply de la clase CatalogueController
$app->get('/catalogue', CatalogueController::class . ':showCatalogue')->setName('catalogue');
// 9- Cuando me llegue una petición POST a la ruta /catalogue, se ejecutará el método handleFormSubmission de la clase CatalogueController
$app->post('/catalogue', CatalogueController::class . ':handleFormSubmission')->setName('bookCreation');

// 10- Cuando me llegue una petición GET a la ruta /catalogue/{id}, se ejecutará el método showBookDetails de la clase BookDetailsController
$app->get('/catalogue/{id}', BookDetailsController::class . ':showBookDetails')->setName('bookDetail')->add(SessionCheckerMiddleware::class);

$app->put('/catalogue/{id}/reviews', BookDetailsController::class . ':showBookDetails')->setName('bookDetail')->add(SessionCheckerMiddleware::class);
$app->put('/catalogue/{id}/rating', BookDetailsController::class . ':showBookDetails')->setName('bookDetail')->add(SessionCheckerMiddleware::class);

$app->get('/forums', ForumsController::class . ':showCurrentForums')->setName('forums')->add(SessionCheckerMiddleware::class);
$app->post('/forums', ForumsController::class . ':createNewForum')->setName('forumsCreation')->add(SessionCheckerMiddleware::class);

$app->get('/forums/{id}/posts', PostsController::class . ':showPosts')->setName('forumPosts')->add(SessionCheckerMiddleware::class);

$app->get('/catalogue/{id}/reviews', BookDetailsController::class . ':showBookReviews')->setName('bookReviews')->add(SessionCheckerMiddleware::class);
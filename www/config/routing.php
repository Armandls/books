<?php
declare(strict_types=1);

// Rutas

use Project\Bookworm\Controller\ApiForumsController;
use Project\Bookworm\Controller\ApiPostsController;
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

// 8- Cuando me llegue una petición GET a la ruta /catalogue, se ejecutará el método showCatalogue de la clase CatalogueController
$app->get('/catalogue', CatalogueController::class . ':showCatalogue')->setName('catalogue');
// 9- Cuando me llegue una petición POST a la ruta /catalogue, se ejecutará el método handleFormSubmission de la clase CatalogueController
$app->post('/catalogue', CatalogueController::class . ':handleFormSubmission')->setName('bookCreation');

// 10- Cuando me llegue una petición GET a la ruta /catalogue/{id}, se ejecutará el método showBookDetails de la clase BookDetailsController
$app->get('/catalogue/{id}', BookDetailsController::class . ':showBookDetails')->setName('bookDetail');

// 11- Cuando me llegue una petición DELETE a la ruta /catalogue/{id}/reviews, se ejecutará el método deleteReview de la clase BookDetailsController
$app->delete('/catalogue/{id}/reviews', BookDetailsController::class . ':deleteReview')->setName('deleteBookReviews');
// 12- Cuando me llegue una petición PUT a la ruta /catalogue/{id}/reviews, se ejecutará el método addReview de la clase BookDetailsController
$app->put('/catalogue/{id}/reviews', BookDetailsController::class . ':addReview')->setName('addBookReview');
// 13- Cuando me llegue una petición DELETE a la ruta /catalogue/{id}/ratings, se ejecutará el método deleteRating de la clase BookDetailsController
$app->delete('/catalogue/{id}/ratings', BookDetailsController::class . ':deleteRating')->setName('deleteRating');
// 14- Cuando me llegue una petición PUT a la ruta /catalogue/{id}/ratings, se ejecutará el método addBookRating de la clase BookDetailsController
$app->put('/catalogue/{id}/ratings', BookDetailsController::class . ':addBookRating')->setName('addBookRating');

// 15- Cuando me llegue una petición GET a la ruta /forums, se ejecutará el método showCurrentForums de la clase ForumsController
$app->get('/forums', ForumsController::class . ':showCurrentForums')->setName('forums');
// 16- Cuando me llegue una petición POST a la ruta /forums, se ejecutará el método createNewForum de la clase ForumsController
$app->post('/forums', ForumsController::class . ':createNewForum')->setName('forumsCreation');

// 17- Cuando me llegue una petición GET a la ruta /forums/{id}/posts, se ejecutará el método showPosts de la clase PostsController
$app->get('/forums/{id}/posts', PostsController::class . ':showPosts')->setName('forumPosts');

// 18- Cuando me llegue una petición POST a la ruta /forums/{id}/posts, se ejecutará el método createNewPost de la clase PostsController
$app->get('/api/forums', ApiForumsController::class . ':showCurrentForums')->setName('getApiForums');
// 19- Cuando me llegue una petición POST a la ruta /forums/{id}/posts, se ejecutará el método createNewPost de la clase PostsController
$app->post('/api/forums', ApiForumsController::class . ':createNewForum')->setName('postApiForums');
// 20- Cuando me llegue una petición GET a la ruta /forums/{id}, se ejecutará el método getForum de la clase ForumsController
$app->get('/api/forums/{id}', ApiForumsController::class . ':getForum')->setName('getForumsID');
// 21- Cuando me llegue una petición PUT a la ruta /forums/{id}, se ejecutará el método updateForum de la clase ForumsController
$app->delete('/api/forums/{id}', ApiForumsController::class . ':deleteForum')->setName('deleteForumsID');

// 22- Cuando me llegue una petición DELETE a la ruta /forums/{id}, se ejecutará el método deleteForum de la clase ForumsController
$app->get('/api/forums/{id}/posts', ApiPostsController::class . ':getApiPosts')->setName('getApiPosts');
// 23- Cuando me llegue una petición POST a la ruta /forums/{id}/posts, se ejecutará el método createNewPost de la clase PostsController
$app->post('/api/forums/{id}/posts', ApiPostsController::class . ':validateApiPost')->setName('validateApiPost');


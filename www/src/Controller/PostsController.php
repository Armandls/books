<?php

namespace Project\Bookworm\Controller;


use GuzzleHttp\Client;
use Project\Bookworm\Model\BookRepository;

use Project\Bookworm\Model\ForumsRepository;
use Project\Bookworm\Model\PostRepository;
use Project\Bookworm\Model\User;
use Project\Bookworm\Model\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
require __DIR__ . '/../../vendor/autoload.php';

class PostsController
{
    private Twig $twig;
    private Messages $flash;
    private UserRepository $userRepository;
    private PostRepository $postRepository;
    private ForumsRepository $forumsRepository;
    private FlashController $flashController;
    private $client;
    private User $user;
    private string $username;
    private string $profile_photo;


    public function __construct(Twig $twig, ForumsRepository $forumsRepository, PostRepository $postRepository,UserRepository $userRepository, FlashController $flashController)
    {
        $this->twig = $twig;
        $this->client = new Client();

        $this->forumsRepository = $forumsRepository;
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;

        $this->flashController = $flashController;
        $this->profile_photo = "";
        $this->username = "unknown";
    }

    public function showCurrentForums(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $errors = [];
        $forums = $this->forumsRepository->fetchAllForums();

        return $this->renderPage($response, $routeParser, $errors, $forums);
    }

    public function createNewForum(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $forums = $this->forumsRepository->fetchAllForums();
        $data = $request->getParsedBody();
        $errors = [];

        return $this->renderPage($response, $routeParser, $errors, $forums);
    }

    private function renderPage($response, $routeParser, $errors, $forums)
    {
        return $this->twig->render($response, 'forums.twig',  [
            'formAction' => $routeParser->urlFor("forums"),
            'formMethod' => "POST",
            'formErrors' => $errors,
            'forums' => $forums,
            'session' => $_SESSION['email'] ?? [],
            'photo' => $this->profile_photo
        ]);
    }

}
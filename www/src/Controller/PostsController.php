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
    private User $user;
    private string $username;
    private string $profile_photo;
    private int $forum_id;


    public function __construct(Twig $twig, ForumsRepository $forumsRepository, PostRepository $postRepository,UserRepository $userRepository, FlashController $flashController)
    {
        $this->twig = $twig;

        $this->forumsRepository = $forumsRepository;
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;

        $this->flashController = $flashController;

        $this->profile_photo = "";
        $this->username = "unknown";
    }

    public function showPosts(Request $request, Response $response, array $args): Response
    {
        $errors = [];
        $this->forum_id = $args['id'];
        $posts = $this->postRepository->getForumPosts($this->forum_id);
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->renderPage($response, $routeParser, $errors, $posts);
    }

    public function createNewForum(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $forums = $this->forumsRepository->fetchAllForums();
        $data = $request->getParsedBody();
        $errors = [];

        return $this->renderPage($response, $routeParser, $errors, $forums);
    }

    private function renderPage($response, $routeParser, $errors, $posts)
    {
        return $this->twig->render($response, 'posts.twig',  [
            'formAction' => $routeParser->urlFor("forumPosts", ['id' => $this->forum_id]),
            'formMethod' => "POST",
            'formErrors' => $errors,
            'posts' => $posts,
            'session' => $_SESSION['email'] ?? [],
            'photo' => $this->profile_photo
        ]);
    }

}
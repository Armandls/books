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
        $session_result = $this->checkSession();
        $errors = [];

        if ($session_result == -1 ){
            return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the landing page.')->withStatus(302);
        }
        if ($session_result == -2) {
            $message = 'You must be logged in to access the forums page.';
            return $this->flashController->redirectToSignIn($request, $response, $message)->withStatus(302);
        }


        $this->forum_id = $args['id'];
        $posts = $this->postRepository->getForumPosts($this->forum_id);
        if ($posts == null) {
            $posts = [];
        }
        $this->setupPosts($posts);
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->renderPage($response, $routeParser, $errors, $posts);
    }

    private function checkSession() {
        if (isset($_SESSION['email'])) {
            $this->user = $this->userRepository->findByEmail($_SESSION['email']);
            $this->profile_photo = "/uploads/{$this->user->profile_picture()}";
            $this->username = $this->user->username();

            if ($this->username == null) {
                return -1;
            } else {
                return 0;
            }
        }

        return -2;
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

    private function setupPosts(array &$posts) {
        if (!empty($posts)) {
            foreach ($posts as $post) {
                // Obtener el usuario correspondiente a la ID de usuario de la publicación
                $user = $this->userRepository->findById($post->getUserId());
                // Establecer el usuario para la publicación
                $post->setUser($user);
            }
        }

    }


}
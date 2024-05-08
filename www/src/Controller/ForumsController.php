<?php

namespace Project\Bookworm\Controller;


use GuzzleHttp\Client;
use Project\Bookworm\Model\BookRepository;

use Project\Bookworm\Model\ForumsRepository;
use Project\Bookworm\Model\User;
use Project\Bookworm\Model\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
require __DIR__ . '/../../vendor/autoload.php';

class ForumsController
{
    private Twig $twig;
    private Messages $flash;
    private UserRepository $userRepository;
    private ForumsRepository $forumsRepository;
    private FlashController $flashController;
    private $client;
    private User $user;
    private string $username;
    private string $profile_photo;


    public function __construct(Twig $twig, ForumsRepository $forumsRepository ,UserRepository $userRepository, FlashController $flashController)
    {
        $this->twig = $twig;
        $this->client = new Client();

        $this->forumsRepository = $forumsRepository;
        $this->userRepository = $userRepository;

        $this->flashController = $flashController;
        $this->profile_photo = "";
        $this->username = "unknown";
    }

    private function checkSession() {
        if (isset($_SESSION['email'])) {
            $this->user = $this->userRepository->findByEmail($_SESSION['email']);
            $this->profile_photo = "/uploads/{$this->user->profile_picture()}";
            $this->username = $this->user->username();

            if ($this->username == null or $this->username == "")  {
                return -1;
            } else {
                $this->books = $this->bookRepository->fetchAllBooks();
                return 0;
            }
        }
        return -2;
    }

    public function showCurrentForums(Request $request, Response $response): Response
    {
        if (isset($_SESSION['email'])) {
            $this->user = $this->userRepository->findByEmail($_SESSION['email']);
            $this->profile_photo = "/uploads/{$this->user->profile_picture()}";
            $this->username = $this->user->username();

            if ($this->username == null)  {
                return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the forums.')->withStatus(302);
            }
            else {
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                $errors = [];
                $forums = $this->forumsRepository->fetchAllForums();
                return $this->renderPage($response, $routeParser, $errors, $forums);
            }
        }
        else {
            return $this->flashController->redirectToSignIn($request, $response, 'You must be logged in to access the forums.')->withStatus(302);
        }
    }

    public function createNewForum(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $forums = $this->forumsRepository->fetchAllForums();
        $data = $request->getParsedBody();
        $errors = $this->validateNewForum($data);

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

    private function validateNewForum(array $data)
    {
        $errors = [];

        $data["title"] = $this->test_input($data['title']);
        if (empty($data["title"])) {
            $errors['title'] = "The title cannot be empty.";
        } else {
            $forum = $this->forumsRepository->findForumByTitle($data['title']);
            if ($forum !== null) {
                return "There's already a forum with this topic!";
            }
        }

        $data["description"] = $this->test_input($data['description']);
        if (empty($data["description"])) {
            $errors['description'] = "The description cannot be empty.";
        }

        if (empty($errors)) {
            $forum = $this->forumsRepository->generateNewForum($data);
            $forumCorrect = $this->forumsRepository->createForum($forum);
            if (!$forumCorrect) {
                $errors['forum'] = "Unexpected error creating new forum";
            }
        }

        return $errors;
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }





}
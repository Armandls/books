<?php

namespace Project\Bookworm\Controller;


use GuzzleHttp\Exception\GuzzleException;
use Project\Bookworm\Model\ForumsRepository;
use Project\Bookworm\Model\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use GuzzleHttp\Client;


class ForumsController
{
    private Twig $twig;
    private UserRepository $userRepository;
    private ForumsRepository $forumsRepository;
    private FlashController $flashController;
    private $client;


    public function __construct(Twig $twig, ForumsRepository $forumsRepository ,UserRepository $userRepository, FlashController $flashController)
    {
        $this->twig = $twig;
        $this->forumsRepository = $forumsRepository;
        $this->userRepository = $userRepository;
        $this->flashController = $flashController;

        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * @throws GuzzleException
     */
    public function showCurrentForums(Request $request, Response $response): Response
    {
        if (isset($_SESSION['email'])) {
            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $profile_photo = "/uploads/{$user->profile_picture()}";
            $username = $user->username();

            if ($username == null)  {
                return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the forums.')->withStatus(302);
            }
            else {
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                $errors = [];
                $forums = $this->forumsRepository->fetchAllForums();
                return $this->renderPage($response, $routeParser, $errors, $forums, $profile_photo, $username);
            }
        }
        else {
            return $this->flashController->redirectToSignIn($request, $response, 'You must be logged in to access the forums.')->withStatus(302);
        }
    }

    public function createNewForum(Request $request, Response $response): Response
    {
        if (isset($_SESSION['email'])) {
            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $profile_photo = "/uploads/{$user->profile_picture()}";
            $username = $user->username();

            if ($username == null) {
                return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the forums.')->withStatus(302);
            } else {
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();

                $forums = $this->forumsRepository->fetchAllForums();
                $data = $request->getParsedBody();

                $errors = $this->validateNewForum($data);

                if (count($errors) > 0) {
                    return $this->renderPage($response, $routeParser, $errors, $forums, $profile_photo, $username);
                } else {
                    $forumCorrect = $this->forumsRepository->createForum($data);
                    if (!$forumCorrect) {
                        $errors['forum'] = "Unexpected error creating new forum";
                    }
                    $forums = $this->forumsRepository->fetchAllForums();
                    return $this->renderPage($response, $routeParser, $errors, $forums, $profile_photo, $username);
                }
            }
        }
        else {
            return $this->flashController->redirectToSignIn($request, $response, 'You must be logged in to access the forums.')->withStatus(302);
        }
    }

    private function renderPage($response, $routeParser, $errors, $forums, $profile_photo, $username): Response
    {
        return $this->twig->render($response, 'forums.twig',  [
            'formAction' => $routeParser->urlFor("forums"),
            'formMethod' => "POST",
            'formErrors' => $errors,
            'forums' => $forums,
            'session' => $_SESSION['email'] ?? [],
            'photo' => $profile_photo
        ]);
    }

    private function validateNewForum(array $data): array
    {
        $errors = [];

        $title = $this->test_input($data['title']);
        if (empty($title)) {
            $errors['title'] = "The title cannot be empty.";
        } else {
            $forum = $this->forumsRepository->findForumByTitle($title);
            if ($forum !== null) {
                $errors['title'] = "There's already a forum with this topic!";
            }
            else {
                $description = $this->test_input($data['description']);
                if (empty($description)) {
                    $errors['description'] = "The description cannot be empty.";
                }
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
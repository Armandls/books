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

class ApiForumsController
{
    private Twig $twig;
    private Messages $flash;
    private UserRepository $userRepository;
    private ForumsRepository $forumsRepository;
    private FlashController $flashController;
    private $client;


    public function __construct(Twig $twig, ForumsRepository $forumsRepository, UserRepository $userRepository, FlashController $flashController)
    {
        $this->twig = $twig;
        $this->client = new Client();

        $this->forumsRepository = $forumsRepository;
        $this->userRepository = $userRepository;

        $this->flashController = $flashController;
    }

    public function showCurrentForums(Request $request, Response $response): Response
    {

        if (isset($_SESSION['email'])) {
            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $profile_photo = "/uploads/{$user->profile_picture()}";
            $username = $user->username();

            if ($username == null)  {
                $errors['message'] = 'This API can only be used by users with a defined username.';

                $response->getBody()->write(json_encode(['errors' => $errors]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            }
            else {
                $forums = $this->forumsRepository->fetchAllForums();
                $forumsData = array_map(function($forum) {
                    return [
                        'id' => $forum->getId(),
                        'title' => $forum->getTitle(),
                        'description' => $forum->getDescription()
                    ];
                }, $forums);

                $response->getBody()->write(json_encode($forumsData));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }
        }
        else {
            $errors['message'] = 'This API can only be used by authenticated users.';

            $response->getBody()->write(json_encode($errors));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

    public function createNewForum(Request $request, Response $response): Response
    {
        if (isset($_SESSION['email'])) {
            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $profile_photo = "/uploads/{$user->profile_picture()}";
            $username = $user->username();

            if ($username == null) {
                $errors['message'] = 'This API can only be used by users with a defined username.';

                $response->getBody()->write(json_encode(['errors' => $errors]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            } else {
                $data = $request->getParsedBody();
                $forum = [];
                $forum['title'] = $data["title"];
                $forum['description'] = $data["description"];

                $errors = $this->validateNewForum($forum);

                if (count($errors) > 0) {
                    $response->getBody()->write(json_encode(['errors' => $errors]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(412);
                }


                $response->getBody()->write(json_encode(['responseData' => $data]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }
        }
        else {
            $errors['message'] = 'This API can only be used by authenticated users.';

            $response->getBody()->write(json_encode($errors));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

    private function validateNewForum(array $data)
    {
        $errors = [];

        $data["title"] = $this->test_input($data['title']);
        if (empty($data["title"])) {
            $errors['title'] = "A required input was missing";
        } else {
            $forum = $this->forumsRepository->findForumByTitle($data['title']);
            if ($forum !== null) {
                return "There's already a forum with this topic!";
            }
        }

        $data["description"] = $this->test_input($data['description']);
        if (empty($data["description"])) {
            $errors['description'] = "A required input was missing";
        }

        if (empty($errors)) {
            $forumCorrect = $this->forumsRepository->createForum($data);
            if (!$forumCorrect) {
                $errors['forum'] = "Unexpected error creating new forum";
            }
        }

        return $errors;
    }

    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function getForum(Request $request, Response $response, array $args): Response
    {

        if (isset($_SESSION['email'])) {
            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $profile_photo = "/uploads/{$user->profile_picture()}";
            $username = $user->username();

            if ($username == null) {
                $errors['message'] = 'This API can only be used by users with a defined username.';

                $response->getBody()->write(json_encode(['errors' => $errors]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            } else {
                $forum_id = $args['id'];
                $forums = $this->forumsRepository->findForumByID($forum_id);
                if ($forums == null) {
                    $errors['message'] = 'Forum with id ' . $forum_id . ' does not exist';

                    $response->getBody()->write(json_encode($errors));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
                }

                $forumsData = [];

                $forumsData['id'] = $forums->getId();
                $forumsData['title'] = $forums->getTitle();
                $forumsData['description'] = $forums->getDescription();

                $response->getBody()->write(json_encode($forumsData));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }
        }
        else{
            $errors['message'] = 'This API can only be used by authenticated users.';

            $response->getBody()->write(json_encode($errors));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

    public function deleteForum(Request $request, Response $response, array $args): Response
    {
        if (isset($_SESSION['email'])) {
            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $profile_photo = "/uploads/{$user->profile_picture()}";
            $username = $user->username();

            if ($username == null) {
                $errors['message'] = 'This API can only be used by users with a defined username.';

                $response->getBody()->write(json_encode(['errors' => $errors]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            } else {
                $forum_id = $args['id'];
                $forums = $this->forumsRepository->findForumByID($forum_id);
                if ($forums == null) {
                    $errors['message'] = 'Forum with id ' . $forum_id . ' does not exist';

                    $response->getBody()->write(json_encode($errors));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
                }

                $this->forumsRepository->deleteForum($forum_id);

                $response->getBody();
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }
        }
        else {
            $errors['message'] = 'This API can only be used by authenticated users.';

            $response->getBody()->write(json_encode($errors));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

}
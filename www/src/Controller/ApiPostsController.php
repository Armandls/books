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

class ApiPostsController
{
    private Twig $twig;
    private Messages $flash;
    private UserRepository $userRepository;
    private ForumsRepository $forumsRepository;
    private PostRepository $postRepository;
    private FlashController $flashController;
    private $client;
    private User $user;
    private ?string $username;
    private ?string $profile_photo;
    private int $forum_id;

    private const MEDIUM_TEXT_LENGTH = 16777215;

    private const TEXT_LENGTH = 65535;


    public function __construct(Twig $twig, ForumsRepository $forumsRepository, UserRepository $userRepository, PostRepository $postRepository, FlashController $flashController)
    {
        $this->twig = $twig;
        $this->client = new Client();

        $this->forumsRepository = $forumsRepository;
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;

        $this->flashController = $flashController;
        $this->profile_photo = "";
        $this->username = "unknown";
    }

    public function getApiPosts(Request $request, Response $response, array $args): Response
    {
        if (isset($_SESSION['email'])) {
            $this->user = $this->userRepository->findByEmail($_SESSION['email']);
            $this->profile_photo = "/uploads/{$this->user->profile_picture()}";
            $this->username = $this->user->username();

            if ($this->username == null) {
                $errors['message'] = 'This API can only be used by users with a defined username.';

                $response->getBody()->write(json_encode(['errors' => $errors]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            } else {
                $this->forum_id = $args['id'];
                $forum = $this->forumsRepository->findForumByID($this->forum_id);
                if ($forum == null) {
                    $errors['message'] = 'Forum with id ' . $this->forum_id . ' does not exist';

                    $response->getBody()->write(json_encode($errors));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
                }
                $posts = $this->postRepository->getForumPosts($this->forum_id);
                $postsData = array_map(function ($post) {
                    return [
                        'id' => $post->getId(),
                        'title' => $post->getTitle(),
                        'contents' => $post->getContents(),
                        'opUsername' => $this->userRepository->findById($post->getUserId())->username(),
                        'opProfilePicture' => $this->userRepository->findById($post->getUserId())->profile_picture(),
                    ];
                }, $posts);

                $response->getBody()->write(json_encode($postsData));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }
        }
        else{
            $errors['message'] = 'This API can only be used by authenticated users.';

            $response->getBody()->write(json_encode($errors));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

    }

    public function validateApiPost(Request $request, Response $response, array $args): Response
    {
        if (isset($_SESSION['email'])) {
            $this->user = $this->userRepository->findByEmail($_SESSION['email']);
            $this->profile_photo = "/uploads/{$this->user->profile_picture()}";
            $this->username = $this->user->username();

            if ( $this->username == null) {
                $errors['message'] = 'This API can only be used by users with a defined username.';

                $response->getBody()->write(json_encode(['errors' => $errors]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            } else {
                $data = $request->getParsedBody();
                $this->forum_id = $args['id'];
                $errors = $this->validateNewPost($data);

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


    private function validateNewPost(array $data)
    {
        $errors = [];

        $data["title"] = $this->test_input($data['title']);
        if (empty($data["title"])) {
            $errors['title'] = "A required input was missing";
        } else {
            if (strlen($data['title']) > 255) {
                $errors['title'] = "The title should contain less than 255 characters.";
            }
            else {
                $forum = $this->forumsRepository->findForumByTitle($data['title']);
                if ($forum !== null) {
                    return "There's already a forum with this topic!";
                }
            }
        }

        $data["description"] = $this->test_input($data['description']);
        if (empty($data["description"])) {
            $errors['description'] = "A required input was missing";
        }
        else {
            if (strlen($data['description']) > self::TEXT_LENGTH) {
                $errors['description'] = "The description should contain less than 65535 characters.";
            }
        }

        if (empty($errors)) {
            $forumID = $this->forum_id;
            $post = $this->postRepository->generateNewPost($data, $forumID, $this->user->id());
            $created = $this->postRepository->createPost($post);
            if (!$created) {
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

    public function getForum(Request $request, Response $response): Response
    {

        if (isset($_SESSION['email'])) {
            $this->user = $this->userRepository->findByEmail($_SESSION['email']);
            $this->profile_photo = "/uploads/{$this->user->profile_picture()}";
            $this->username = $this->user->username();

            if ($this->username == null) {
                $errors['message'] = 'This API can only be used by users with a defined username.';

                $response->getBody()->write(json_encode(['errors' => $errors]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            } else {
                $forumID = 1;
                $forums = $this->forumsRepository->findForumByID($forumID);
                $forumsData = array_map(function ($forum) {
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

}
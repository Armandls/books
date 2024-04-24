<?php

namespace Project\Bookworm\Controller;

use Project\Bookworm\Model\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages;
use Slim\Views\Twig;

class UserProfile
{
    private Twig $twig;
    private UserRepository $userRepository;
    private FlashController $flashController;

    public function __construct(Twig $twig, UserRepository $userRepository, FlashController $flashController)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->flashController = $flashController;
    }

    public function showProfile(Request $request, Response $response): Response{

        $message = '';

        if (isset($_SESSION['email'])) {
            return $this->twig->render($response, 'user-profile.twig', [
                'email' => $_SESSION['email']
            ]);
        } else {
            $message = 'You must be logged in to access the user profile page.';
            return $this->flashController->redirectToSignIn($request, $response, $message)->withStatus(302);
        }
    }

    public function editProfile(Request $request, Response $response): Response{

            $message = '';

            if (isset($_SESSION['email'])) {
                return $this->twig->render($response, 'edit-profile.twig', [
                    'email' => $_SESSION['email']
                ]);
            } else {
                $message = 'You must be logged in to access the edit profile page.';
                return $this->flashController->redirectToSignIn($request, $response, $message);
            }
    }
}
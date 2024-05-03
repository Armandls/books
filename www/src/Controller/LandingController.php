<?php

namespace Project\Bookworm\Controller;

use Project\Bookworm\Model\UserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class LandingController
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

    public function apply(Request $request, Response $response): Response
    {
        if (isset($_SESSION['email'])) {
            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $profile_photo = "/uploads/{$user->profile_picture()}";
            $username = $user->username();

            if ($username == null) {
                return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the landing page.')->withStatus(302);
            }
            else {
                // Renderizar la plantilla de landing
                return $this->twig->render($response, 'landing.twig', [
                    'session' => $_SESSION['email'] ?? [],
                    'photo' => $profile_photo
                ]);
            }
        } else {
            // Renderizar la plantilla de landing
            return $this->twig->render($response, 'landing.twig', [
                'session' => $_SESSION['email'] ?? [],
            ]);
        }
    }



}
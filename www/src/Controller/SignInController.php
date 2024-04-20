<?php

namespace Project\Bookworm\Controller;

use Project\Bookworm\Model\UserRepository;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


final class SignInController
{

    private Twig $twig;
    private UserRepository $userRepository;

    private Messages $flash;

    public function __construct(Twig $twig, UserRepository $userRepository, Messages $flash)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->flash = $flash;
    }


    // Método que muestra el formulario
    public function showForm(Request $request, Response $response): Response
    {
        //$messages = $this->flash->getMessages();

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render($response, 'sign-in.twig', []);
    }

    // Método que maneja el envío del formulario -> Validación de datos
    public function handleFormSubmission(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $errors = $this->validate($data, $this->userRepository);

        if (count($errors) > 0) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $this->twig->render($response, 'sign-in.twig', [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor("login-form"),
                'formMethod' => "POST"
            ]);
        }
        else  {
            $_SESSION['email'] = $data['email'];
            return $response->withHeader('Location', '/')->withStatus(302);
        }
    }

    /*
    private function validate(array $data, userRepository $userRepository): array
    {
        $errors = [];

        // Errores email
        if (empty($data['email'])) {
            $errors['email'] = 'The email address is not valid.';
        }
        else {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'The email address is not valid.';
            }
            else {
                $user = $userRepository->findByEmail($data['email']);
                $storedPassword = $user->password();

                if ($data['password'] != $storedPassword) {
                    $errors['password'] = 'Your email and/or password are incorrect.';
                }

            }
        }

        return $errors;
    }*/
}
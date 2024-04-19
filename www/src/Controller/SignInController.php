<?php

namespace Project\Bookworm\Controller;

use Project\Bookworm\Model\UserRepository;
use Slim\Flash\Messages;
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

        //$routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render($response, 'sign-in.twig', []);
    }

    // Método que maneja el envío del formulario -> Validación de datos
    /*public function handleFormSubmission(Request $request, Response $response): Response
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
    }*/

    /*private function validate(array $data, userRepository $userRepository): array
    {
        $errors = [];

        // Errores email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || empty($data['email'])) {
            $errors['email'] = 'The email address is not valid.';
        }
        else {
            if (!strpos($data['email'], '@salle.url.edu')){
                $errors['email'] = 'Only emails from the domain @salle.url.edu are accepted.';
            }
            else {
                $user = $userRepository->findByEmail($data['email']);
                if ($user == null) {
                    $errors['email'] = 'User with this email address does not exist.';
                }
                else {
                    $storedPasswordHash = $user->password();
                    if ($data['password'] != $storedPasswordHash) {
                        $errors['password'] = 'Your email and/or password are incorrect.';
                    }
                }
            }
        }


        // Errores password

        if (strlen($data['password']) < 7) {
            $errors['password'] = 'The password must contain at least 7 characters.';
        }
        else {
            if (!preg_match('/[A-Z]/', $data['password']) || !preg_match('/[a-z]/', $data['password']) || !preg_match('/[0-9]/', $data['password'])) {
                $errors['password'] = 'The password must contain both upper and lower case letters and numbers.';
            }
        }

        return $errors;
    }*/
}
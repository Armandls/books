<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller;

use DateTime;
use Exception;
use Project\Bookworm\Model\User;
use Project\Bookworm\Model\UserRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class SignUpController
{

    public function __construct(
        private Twig $twig,
        private UserRepository $userRepository // Quita el punto y coma aquí
    )
    {
    }


    // Método que muestra el formulario
    public function showForm(Request $request, Response $response): Response
    {

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render($response, 'sign-up.twig', [
            'formAction' => $routeParser->urlFor("get-sign-up"),
            'formMethod' => "POST"
        ]);
    }


    // Método que maneja el envío del formulario -> Validación de datos
    public function handleFormSubmission(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $errors = $this->validate($data, $this->userRepository);

        if (count($errors) > 0) {

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $this->twig->render($response, 'sign-up.twig', [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor("handle-form"),
                'formMethod' => "POST"
            ]);
        }
        else  {
            // Crear un nuevo usuario
                $user = new User(
                    1,
                    $data['email'] ?? '',
                    $data['password'] ?? '',
                    $data['username'] ?? '',
                    $data['profile_picture'] ?? '',
                    new DateTime(),
                    new DateTime()
                );

                $this->userRepository->save($user);
                $_SESSION['email'] = $data['email'];
                return $response->withHeader('Location', '')->withStatus(302);
        }
    }

    private function validate(array $data, userRepository $userRepository): array
    {
        $errors = [];

        // Errores email
        if (empty($data['email'])) {
            $errors['email'] = 'The email field is required.';
        }
        else {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'The email address is not valid.';
            }
            else {
                if ($userRepository->findByEmail($data['email']) != null) {
                    $errors['email'] = 'The email address is already registered.';
                }
                else {
                    if (strlen($data['password']) < 6 || !preg_match('/[0-9]/', $data['password'])) {
                        $errors['password'] = 'The password must be at least 6 characters long and contain at least one number.';
                    }
                    else {
                        if (empty($data['repeatPassword']) || $data['password'] != $data['repeatPassword']) {
                            $errors['repeatPassword'] = 'Passwords do not match.';
                        }
                    }
                }
            }
        }
        return $errors;
    }
}
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

    /*
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
                    intval($data['numBitcoins'] ?? 0), // Convierte a entero y usa 0 si no se proporciona ningún valor
                    new DateTime(),
                    new DateTime()
                );

                $this->userRepository->save($user);
                return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }
    }

    private function validate(array $data, userRepository $userRepository): array
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
                if (strlen($data['password']) < 7) {
                        $errors['password'] = 'The password must contain at least 7 characters.';
                }
                else {
                    // Verificar si la contraseña contiene al menos una letra mayúscula, una letra minúscula y un número
                    if (empty($data['repeatPassword']) || $data['password'] != $data['repeatPassword']) {
                        $errors['repeatPassword'] = 'Passwords do not match.';
                    }
                    else {
                        // Errores repeatPassword
                        if (!preg_match('/[A-Z]/', $data['password']) || !preg_match('/[a-z]/', $data['password']) || !preg_match('/[0-9]/', $data['password'])) {
                            $errors['password'] = 'The password must contain both upper and lower case letters and numbers.';
                        }
                        else {
                            if ($userRepository->findByEmail($data['email']) != null) {
                                $errors['email'] = 'The email address is already registered.';
                            }

                            if (empty($data['numBitcoins'])) {
                            }
                            else {
                                if (!is_numeric($data['numBitcoins']) || $data['numBitcoins'] < 0 || $data['numBitcoins'] > 40000) {
                                    if ($data['numBitcoins'] < 0 || $data['numBitcoins'] > 40000) {
                                        $errors['numBitcoins'] = 'Sorry, the number of Bitcoins is either below or above the limits.';
                                    }

                                    if (!is_numeric($data['numBitcoins'])) {
                                        $errors['numBitcoins'] = 'The number of Bitcoins is not a valid number.';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $errors;
    }*/
}
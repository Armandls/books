<?php

namespace Project\Bookworm\Controller;

use Project\Bookworm\Model\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class UserProfile
{
    private Twig $twig;
    private UserRepository $userRepository;
    private FlashController $flashController;

    private const UPLOADS_DIR = __DIR__ . '/../../uploads';

    // We use this const to define the extensions that we are going to allow
    private const ALLOWED_EXTENSIONS = ['png', 'jpg', 'gif', 'svg'];
    private const ALLOWED_MIME_TYPES = ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml'];



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
        $errors = [];

        $data = $request->getParsedBody();
        $this->validate($data, $this->userRepository, $errors);

        // Si hay algun error tanto en el username o en el email, mostramos el formulario con los errores
        if (count($errors) > 0) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $this->twig->render($response, 'user-profile.twig', [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor("/profile"),
                'formMethod' => "POST"
            ]);
        }
        else  {
            $uploadedFiles = $request->getUploadedFiles();  // Get the uploaded files -> reference to the files that have been uploaded in the server

            // Error en el caso que hayn introducido más de un archivo
            if (count($uploadedFiles['files']) !== 1) {
                $errors['numFiles'] = 'Only one file upload is allowed.';

                $routeParser = RouteContext::fromRequest($request)->getRouteParser();

                return $this->twig->render($response, 'user-profile.twig', [
                    'formErrors' => $errors,
                    'formData' => $data,
                    'formAction' => $routeParser->urlFor("/profile"),
                    'formMethod' => "POST"
                ]);
            }
            else {
                // error en el caso que me llegue un archivo que no sea correcto (error en la subida)
                /** @var UploadedFileInterface $uploadedFile */
                if ($uploadedFiles['file']->getError() !== UPLOAD_ERR_OK) {
                    $errors['errorBadFile'] = "An unexpected error occurred uploading the file " . $uploadedFiles['file']->getClientFilename();

                    $routeParser = RouteContext::fromRequest($request)->getRouteParser();

                    return $this->twig->render($response, 'user-profile.twig', [
                        'formErrors' => $errors,
                        'formData' => $data,
                        'formAction' => $routeParser->urlFor("/profile"),
                        'formMethod' => "POST"
                    ]);
                }
                else {
                    $uploadedFile = $uploadedFiles['file'];

                    $name = $uploadedFile->getClientFilename();  // Get the name of the file, nos el path completo

                    $fileInfo = pathinfo($name);  // Get the information of the file

                    $format = $fileInfo['extension'];   // Get the extension of the file

                    // Error en el caso que el archivo no tenga una extensión válida
                    if (!$this->isValidFormat($format)) {
                        $errors[] = "The received file extension ". $name . " is not valid";

                        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

                        return $this->twig->render($response, 'user-profile.twig', [
                            'formErrors' => $errors,
                            'formData' => $data,
                            'formAction' => $routeParser->urlFor("/profile"),
                            'formMethod' => "POST"
                        ]);
                    }
                    else {
                        // Comprovar mimetype del archivo
                        if (!in_array($uploadedFile->getClientMediaType(), self::ALLOWED_MIME_TYPES, true)) {
                            $errors['errorMimeType'] = "The received file MIME type is not valid";

                            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

                            return $this->twig->render($response, 'user-profile.twig', [
                                'formErrors' => $errors,
                                'formData' => $data,
                                'formAction' => $routeParser->urlFor("/profile"),
                                'formMethod' => "POST"
                            ]);
                        }
                        else {
                            // Comprovar tamaño del archivo -> 400x400

                            //Name regenerated
                            $customName = uniqid('file_');

                            // Move the file to the uploads directory
                            $uploadedFile->moveTo(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $customName);

                            return $response->withHeader('Location', '/')->withStatus(302);
                        }
                    }
                }
            }
        }
    }

    private function validate(array $data, UserRepository $userRepository, array &$errors): void
    {
        // Error email
        if ($data['email'] != $_SESSION['email']) {
            $errors['email'] = 'The email address is not valid. Please don\'t change the email address.';
        }
        else {
            // Error username
            if ($data['username'] == null) {
                $errors['username'] = 'The username field is required. Please enter a new username';
            }
            else {
                if ($this->userRepository->findByUsername($data['username']) == null) {
                    $errors['username'] = 'This username is already taken. Please choose another one.';
                }
            }
        }
    }

    private function isValidFormat(string $extension): bool
    {
        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }
}
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
    private Messages $flash;

    private const UPLOADS_DIR = __DIR__ . '/../../public/uploads';

    // We use this const to define the extensions that we are going to allow
    private const ALLOWED_EXTENSIONS = ['png', 'jpg', 'gif', 'svg'];
    private const ALLOWED_MIME_TYPES = ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml'];

    private const MAX_IMAGE_SIZE = 1048576;



    public function __construct(Twig $twig, UserRepository $userRepository, FlashController $flashController, Messages $flash)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->flashController = $flashController;
        $this->flash = $flash;
    }

    public function showProfile(Request $request, Response $response): Response{

        $message = '';

        if (isset($_SESSION['email'])) {

            $messages = $this->flash->getMessages();

            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $username = $user->username();

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            return $this->twig->render($response, 'user-profile.twig', [
                'formAction' => $routeParser->urlFor("show-profile"),
                'formMethod' => "POST",
                'email' => $_SESSION['email'],
                'username' => $username ?? '',
                'flash' => $messages['flash'] ?? []
            ]);
        } else {
            $message = 'You must be logged in to access the user profile page.';
            return $this->flashController->redirectToSignIn($request, $response, $message)->withStatus(302);
        }
    }

    public function editProfile(Request $request, Response $response): Response{
        $errors = [];

        $data = $request->getParsedBody();

        $user = $this->userRepository->findByEmail($_SESSION['email']);
        $username = $user->username();

        $this->validate($data, $this->userRepository, $errors, $username);

        // Si hay algun error tanto en el username o en el email, mostramos el formulario con los errores
        if (count($errors) > 0) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $this->twig->render($response, 'user-profile.twig', [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor("edit-profile"),
                'formMethod' => "POST",
                'email' => $_SESSION['email'],
                'username' => $username ?? '',
            ]);
        }
        else  {
            $uploadedFiles = $request->getUploadedFiles();  // Get the uploaded files -> reference to the files that have been uploaded in the server

            if (!$uploadedFiles['file']->getError() == UPLOAD_ERR_NO_FILE) {   // Error del campo error -> 4 -> No se ha subido ningún archivo
                // Error en el caso que hayn introducido más de un archivo
                if (count($uploadedFiles) > 1) {
                    $errors['file'] = 'Only one file upload is allowed.';

                    $routeParser = RouteContext::fromRequest($request)->getRouteParser();

                    return $this->twig->render($response, 'user-profile.twig', [
                        'formErrors' => $errors,
                        'formData' => $data,
                        'formAction' => $routeParser->urlFor("edit-profile"),
                        'formMethod' => "POST",
                        'email' => $_SESSION['email'],
                        'username' => $username ?? '',
                    ]);
                }
                else {
                    // error en el caso que me llegue un archivo que no sea correcto (error en la subida)
                    /** @var UploadedFileInterface $uploadedFiles */
                    if ($uploadedFiles['file']->getError() !== UPLOAD_ERR_OK && !empty($uploadedFiles['file'])) {
                        $errors['file'] = "An unexpected error occurred uploading the file " . $uploadedFiles->getClientFilename();

                        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

                        return $this->twig->render($response, 'user-profile.twig', [
                            'formErrors' => $errors,
                            'formData' => $data,
                            'formAction' => $routeParser->urlFor("edit-profile"),
                            'formMethod' => "POST",
                            'email' => $_SESSION['email'],
                            'username' => $username ?? '',
                        ]);
                    }
                    else {
                        $fileSize = $uploadedFiles['file']->getSize();

                        if ($fileSize > self::MAX_IMAGE_SIZE) {
                            $errors['file'] = "The file size exceeds the maximum allowed size of 1MB";

                            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

                            return $this->twig->render($response, 'user-profile.twig', [
                                'formErrors' => $errors,
                                'formData' => $data,
                                'formAction' => $routeParser->urlFor("edit-profile"),
                                'formMethod' => "POST",
                                'email' => $_SESSION['email'],
                                'username' => $username ?? '',
                            ]);
                        }
                        else {
                            $uploadedFile = $uploadedFiles['file'];

                            $name = $uploadedFile->getClientFilename();  // Get the name of the file, nos el path completo

                            $fileInfo = pathinfo($name);  // Get the information of the file

                            $format = $fileInfo['extension'];   // Get the extension of the file

                            // Error en el caso que el archivo no tenga una extensión válida
                            if (!$this->isValidFormat($format)) {
                                $errors['file'] = "The received file extension ". $name . " is not valid";

                                $routeParser = RouteContext::fromRequest($request)->getRouteParser();

                                return $this->twig->render($response, 'user-profile.twig', [
                                    'formErrors' => $errors,
                                    'formData' => $data,
                                    'formAction' => $routeParser->urlFor("edit-profile"),
                                    'formMethod' => "POST",
                                    'email' => $_SESSION['email'],
                                    'username' => $username ?? '',
                                ]);
                            }
                            else {
                                // Comprovar mimetype del archivo
                                if (!in_array($uploadedFile->getClientMediaType(), self::ALLOWED_MIME_TYPES, true)) {
                                    $errors['file'] = "The received file MIME type is not valid";

                                    $routeParser = RouteContext::fromRequest($request)->getRouteParser();

                                    return $this->twig->render($response, 'user-profile.twig', [
                                        'formErrors' => $errors,
                                        'formData' => $data,
                                        'formAction' => $routeParser->urlFor("edit-profile"),
                                        'formMethod' => "POST",
                                        'email' => $_SESSION['email'],
                                        'username' => $username ?? '',
                                    ]);
                                }
                                else {
                                    // Comprovar tamaño del archivo -> 400x400
                                    $imageSize = getimagesize($uploadedFile->getStream()->getMetadata('uri'));
                                    $imageWidth = $imageSize[0]; // Ancho de la imagen
                                    $imageHeight = $imageSize[1]; // Alto de la imagen

                                    if ($imageWidth > 400 || $imageHeight > 400) {
                                        $errors['file'] = "The image dimensions exceed the maximum allowed size of 400x400 pixels";

                                        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

                                        return $this->twig->render($response, 'user-profile.twig', [
                                            'formErrors' => $errors,
                                            'formData' => $data,
                                            'formAction' => $routeParser->urlFor("edit-profile"),
                                            'formMethod' => "POST",
                                            'email' => $_SESSION['email'],
                                            'username' => $username ?? '',
                                        ]);
                                    }
                                    else {
                                        if ($username != null) {
                                            //Name regenerated
                                            $customName = uniqid('file_') . '.'. $format;

                                            // Move the file to the uploads directory
                                            $uploadedFile->moveTo(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $customName);

                                            $email = $_SESSION['email'];
                                            $_SESSION['profile-photo'] = $customName;

                                            // update the user profile picture and username
                                            $this->userRepository->updateProfilePicture($email, $customName);

                                            if ($data['username'] == null) {
                                                $_SESSION['username'] = $username;

                                                return $response->withHeader('Location', '/')->withStatus(302);
                                            }
                                            else {
                                                $email = $_SESSION['email'];
                                                $_SESSION['username'] = $data['username'];

                                                // update the user profile picture and username
                                                $this->userRepository->updateUsername($email, $data['username']);

                                                return $response->withHeader('Location', '/')->withStatus(302);
                                            }
                                        }
                                        else {
                                            //Name regenerated
                                            $customName = uniqid('file_') . '.'. $format;

                                            // Move the file to the uploads directory
                                            $uploadedFile->moveTo(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $customName);

                                            $email = $_SESSION['email'];
                                            $_SESSION['profile-photo'] = $customName;
                                            $_SESSION['username'] = $data['username'];

                                            // update the user profile picture and username
                                            $this->userRepository->updateProfilePicture($email, $customName);
                                            $this->userRepository->updateUsername($email, $data['username']);

                                            return $response->withHeader('Location', '/')->withStatus(302);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else {
                if ($username != null) {
                    if ($data['username'] == null) {
                        $_SESSION['username'] = $username;

                        return $response->withHeader('Location', '/')->withStatus(302);
                    }
                    else {
                        $email = $_SESSION['email'];
                        $_SESSION['username'] = $data['username'];

                        // update the user profile picture and username
                        $this->userRepository->updateUsername($email, $data['username']);

                        return $response->withHeader('Location', '/')->withStatus(302);
                    }
                }
                else {
                    $email = $_SESSION['email'];
                    $_SESSION['username'] = $data['username'];

                    // update the user profile picture and username
                    $this->userRepository->updateUsername($email, $data['username']);


                    return $response->withHeader('Location', '/')->withStatus(302);
                }
            }
        }
    }

    private function validate(array $data, UserRepository $userRepository, array &$errors, ?string $username): void
    {
        // Error email
        if ($data['email'] != $_SESSION['email']) {
            $errors['email'] = 'The email address is not valid. Please don\'t change the email address.';
        }
        else {
            if ($data['username'] == null) {
                if ($username == null) {
                    $errors['username'] = 'The username field is required. Please enter a new username';
                }
            }
            else {
                if ($data['username'] == $username) {

                } else {
                    if ($userRepository->findByUsername($data['username']) !== null) {
                        $errors['username'] = 'This username is already taken. Please choose another one.';
                    }
                }
            }
        }
    }

    private function isValidFormat(string $extension): bool
    {
        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }
}
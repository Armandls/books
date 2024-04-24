<?php

namespace Project\Bookworm\Controller;

use Project\Bookworm\Model\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;

class UserProfile
{
    private Twig $twig;
    private UserRepository $userRepository;
    private FlashController $flashController;

    private const UPLOADS_DIR = __DIR__ . '/../../uploads';

    private const UNEXPECTED_ERROR = "An unexpected error occurred uploading the file '%s'...";

    private const INVALID_EXTENSION_ERROR = "The received file extension '%s' is not valid";

    // We use this const to define the extensions that we are going to allow
    private const ALLOWED_EXTENSIONS = ['png', 'jpg', 'gif', 'svg'];


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

        $data = $request->getParsedBody();

        $uploadedFiles = $request->getUploadedFiles();  // Get the uploaded files -> reference to the files that have been uploaded in the server

        $errors = [];

        /** @var UploadedFileInterface $uploadedFile */
        foreach ($uploadedFiles['files'] as $uploadedFile) {
            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                $errors[] = sprintf(
                    self::UNEXPECTED_ERROR,
                    $uploadedFile->getClientFilename()
                );
                continue;
            }

            $name = $uploadedFile->getClientFilename();  // Get the name of the file, nos el path completo

            $fileInfo = pathinfo($name);  // Get the information of the file

            // COMPROBAR EL MIMETYPE DEL FICHERO Y COMPARARLO CON EL QUE NOSOTROS QUEREMOS
            // COMPROBAR EL TAMAÑO DEL FICHERO
            $format = $fileInfo['extension'];   // Get the extension of the file

            // We should validate the format of the file
            if (!$this->isValidFormat($format)) {
                $errors[] = sprintf(self::INVALID_EXTENSION_ERROR, $format);
                continue;
            }

            // We should generate a custom name here instead of using the one coming form the form
            // Here we are using the original name, but we should generate a new one with a UUID for example or a hash
            $uploadedFile->moveTo(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $name);
        }

        return $this->twig->render($response, 'user-profile.twig', [
            'errors' => $errors,
        ]);
    }

    private function isValidFormat(string $extension): bool
    {
        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }
}
<?php

namespace Project\Bookworm\Controller;

use DateTime;
use GuzzleHttp\Client;
use Project\Bookworm\Model\Book;
use Project\Bookworm\Model\BookRepository;

use Project\Bookworm\Model\User;
use Project\Bookworm\Model\UserRepository;
use Project\Bookworm\Utils\BookCreationChecker;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

use Slim\Flash\Messages;
require __DIR__ . '/../../vendor/autoload.php';

class CatalogueController
{
    private Twig $twig;
    private BookRepository $bookRepository;
    private UserRepository $userRepository;
    private FlashController $flashController;
    private $client;
    private User $user;
    private string $username;
    private string $profile_photo;
    private array $books;

    private const UPLOADS_DIR = __DIR__ . '/../../public/uploads';

    // We use this const to define the extensions that we are going to allow
    private const ALLOWED_EXTENSIONS = ['png', 'jpg', 'gif', 'svg'];
    private const ALLOWED_MIME_TYPES = ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml'];
    private const MAX_IMAGE_SIZE = 1048576;
    private string $customName;

    public function __construct(Twig $twig, BookRepository $bookRepository, UserRepository $userRepository, FlashController $flashController)
    {
        $this->twig = $twig;
        $this->bookRepository = $bookRepository;
        $this->client = new Client();
        $this->userRepository = $userRepository;
        $this->flashController = $flashController;
        $this->profile_photo = "";
        $this->username = "unknown";
        $this->checkSession();
    }

    private function checkSession() {
        if (isset($_SESSION['email'])) {
            $this->user = $this->userRepository->findByEmail($_SESSION['email']);
            $this->profile_photo = "/uploads/{$this->user->profile_picture()}";
            $this->username = $this->user->username();

            if ($this->username == null or $this->username == "")  {
                return -1;
            } else {
                $this->books = $this->bookRepository->fetchAllBooks();
                return 0;
            }
        }

        return -2;
    }

    public function showCatalogue(Request $request, Response $response): Response
    {
        $session_result = $this->checkSession();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $errors = [];

        if ($session_result == -1 ){
            return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the landing page.')->withStatus(302);
        }
        if ($session_result == -2) {
            $message = 'You must be logged in to access the user profile page.';
            return $this->flashController->redirectToSignIn($request, $response, $message)->withStatus(302);
        }
        return $this->renderPage($response, $routeParser, $errors);
    }

    public function handleFormSubmission(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $errors = $this->validateForms($data);
        $this->books = $this->bookRepository->fetchAllBooks();

        // If there are errors, render the form again with the errors
        if (count($errors) > 0) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $this->renderPage($response,$routeParser,$errors);
        }

        if ($data['formType'] == 'fullForm') {
            $fileCorrect = $this->checkCorrectFile($response, $request, $errors);
            if ($fileCorrect) {
                $data['cover_image'] = $this->customName;
                $book = $this->bookRepository->generateBook($data);
                $this->bookRepository->createBook($book);

                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                $redirectUrl = $routeParser->urlFor("bookCreation");
                return $response->withHeader('Location', $redirectUrl)->withStatus(302);
            }
        }



        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $this->renderPage($response,$routeParser,$errors);

    }

    private function findBookByISBN($isbn) {
        $apiUrl = "https://openlibrary.org/isbn/$isbn.json";

        $response = $this->client->request('GET', $apiUrl);
        $dataDecode = json_decode($response->getBody(), true);

        try {
            $title = $dataDecode["title"] ?? "";
            $pageNumber = $dataDecode["number_of_pages"] ?? 0;
            $key = $dataDecode["key"];

            $work = $dataDecode["works"][0]["key"];
            $apiUrl = "https://openlibrary.org$work.json";
            $response = $this->client->request('GET', $apiUrl);

            $dataDecode = json_decode($response->getBody(), true);

            // Create DateTime objects for created_at and updated_at (current time)
            $createdAt = new DateTime($dataDecode["created"]["value"]);
            $updatedAt = new DateTime($dataDecode["last_modified"]["value"]);

            // Generate cover URL
            $coverId = $dataDecode["covers"][0] ?? 0;
            $coverImageUrl = "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg" ?? "";


            $description = $dataDecode["description"]["value"] ?? ""; // Si retorna array -> cogemos valor del array
            if ($description == "") {
                $description = $dataDecode["description"] ?? ""; // Sino probamos con descripcion a secas
            }

            // Hardcodeamos eliminar source e informacion que no queremos
            if ($description != ""){
                $description = explode("([", $description)[0];
                //$description = explode("[S", $description)[0];
            }

            $authorEndpoint = $dataDecode["authors"][0]["author"]["key"] ?? "";
            $apiUrl = "https://openlibrary.org$authorEndpoint.json";
            $response = $this->client->request('GET', $apiUrl);
            $dataDecode = json_decode($response->getBody(), true);
            $author = $dataDecode["name"];

            $apiUrl = "https://openlibrary.org$key.json";
            $response = $this->client->request('GET', $apiUrl);
            $dataDecode = json_decode($response->getBody(), true);

            return new Book(0, $title, $author, $description, $pageNumber, $coverImageUrl, $createdAt, $updatedAt);
        } catch (\Exception $exception) {

            return null;
        }
    }


    private function validateForms(array $data)
    {
        $errors = [];
        $formType = $data['formType'];

        switch ($formType) {
            case 'isbnForm':
                $book = $this->findBookByISBN($data['isbn']);
                if ($book == null) {
                    $errors['isbn'] = 'The ISBN code is not valid.';
                    break;
                }

                if ($this->bookRepository->findBookByTitle($book->getTitle())) {
                    $errors['isbn'] = "This book already exists.";
                    break;
                }

                $bookCreated = $this->bookRepository->createBook($book);
                if (!$bookCreated) {
                    $errors['isbn'] = "The Book couldn't be created.";
                    break;
                }


                break;
            case 'fullForm':
                $errors =  BookCreationChecker::checkCorrectForm($data, $this->bookRepository, $errors);
                //if (empty($errors)) {
                //    $book = $this->bookRepository->generateBook($data);
                //    $this->bookRepository->createBook($book);
                //}
                break;
        }
        return $errors;
    }

    private function checkCorrectFile($response, $request,array &$errors)
    {

        $uploadedFiles = $request->getUploadedFiles();  // Get the uploaded files -> reference to the files that have been uploaded in the server
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if (!$uploadedFiles['cover_image']->getError() == UPLOAD_ERR_NO_FILE) {   // Error del campo error -> 4 -> No se ha subido ningún archivo
            // Error en el caso que hayn introducido más de un archivo
            if (count($uploadedFiles) > 1) {
                $errors['file'] = 'Only one file upload is allowed.';
                return false;

            }

            /** @var UploadedFileInterface $uploadedFiles */
            if ($uploadedFiles['cover_image']->getError() !== UPLOAD_ERR_OK && !empty($uploadedFiles['cover_image'])) {
                $errors['file'] = "An unexpected error occurred uploading the file " . $uploadedFiles->getClientFilename();
                return false;
            }

            $fileSize = $uploadedFiles['cover_image']->getSize();
            if ($fileSize > self::MAX_IMAGE_SIZE) {
                $errors['file'] = "The file size exceeds the maximum allowed size of 1MB";
                return false;
            }

            $uploadedFile = $uploadedFiles['cover_image'];
            $name = $uploadedFile->getClientFilename();  // Get the name of the file, nos el path completo
            $fileInfo = pathinfo($name);  // Get the information of the file
            $format = $fileInfo['extension'];   // Get the extension of the file

            // Error en el caso que el archivo no tenga una extensión válida
            if (!$this->isValidFormat($format)) {
                $errors['file'] = "The received file extension " . $name . " is not valid";
                return false;
            }
            if (!in_array($uploadedFile->getClientMediaType(), self::ALLOWED_MIME_TYPES, true)) {
                $errors['file'] = "The received file MIME type is not valid";
                return false;
            }

            //Name regenerated
            $this->customName = uniqid('file_') . '.' . $format;
            // Move the file to the uploads directory
            $uploadedFile->moveTo(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $this->customName);
            return true;
        }
        $errors['file'] = 'You must upload a cover image for the book.';
        return false;
    }

    private function isValidFormat(string $extension): bool
    {
        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }

    private function renderPage($response, $routeParser, $errors)
    {
        return $this->twig->render($response, 'catalogue.twig',  [
            'formAction' => $routeParser->urlFor("bookCreation"),
            'formMethod' => "POST",
            'formErrors' => $errors,
            'books' => $this->books,
            'session' => $_SESSION['email'] ?? [],
            'photo' => $this->profile_photo
        ]);
    }
}
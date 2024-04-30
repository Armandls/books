<?php

namespace Project\Bookworm\Controller;

use DateTime;
use GuzzleHttp\Client;
use Project\Bookworm\Model\Book;
use Project\Bookworm\Model\BookRepository;

use Project\Bookworm\Utils\BookCreationChecker;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
require __DIR__ . '/../../vendor/autoload.php';

class CatalogueController
{
    private Twig $twig;
    private BookRepository $bookRepository;
    private Messages $flash;
    private $client;


    public function __construct(Twig $twig, BookRepository $bookRepository, Messages $flash)
    {
        $this->twig = $twig;
        $this->bookRepository = $bookRepository;
        $this->flash = $flash;
        $this->client = new Client();


    }

    public function showCatalogue(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $books = $this->bookRepository->fetchAllBooks();

        return $this->twig->render($response, 'catalogue.twig',  [
            'formAction' => $routeParser->urlFor("catalogue"),
            'formMethod' => "GET",
            'books' => $books
        ]);
    }

    public function handleFormSubmission(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $errors = $this->validateForms($data);
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        // If there are errors, render the form again with the errors
        if (!empty($errors)) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $books = $this->bookRepository->fetchAllBooks();
            return $this->twig->render($response, 'catalogue.twig', [
                'formErrors' => $errors,
                'formData' => $data,
                'formAction' => $routeParser->urlFor("catalogue"),
                'formMethod' => "POST",
                'books' => $books
            ]);
        }

        // If there are no errors, redirect the user to a different URL
        $redirectUrl = $routeParser->urlFor("catalogue");
        return $response->withHeader('Location', $redirectUrl)->withStatus(302);

    }

    private function findBookByISBN($isbn) {
        $apiUrl = "https://openlibrary.org/isbn/$isbn.json";
        $response = $this->client->request('GET', $apiUrl);
        $data = json_decode($response->getBody(), true);
        $endPointUrl = "https://openlibrary.org/works/";
        try {
            $title = $data["title"] ?? "";
            $description = $data["description"] ?? "";
            $pageNumber = $data["number_of_pages"] ?? 0;

            $author = $data["authors"][0]["name"] ?? "";

            // Generate cover URL
            $coverId = isset($data["covers"][0]) ? $data["covers"][0] : "";
            $coverImageUrl = "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg";

            // Create DateTime objects for created_at and updated_at (current time)
            $createdAt = new DateTime();
            $updatedAt = new DateTime();

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
                } else {
                    $bookCreated = $this->bookRepository->createBook($book);
                    if (!$bookCreated) {
                        $errors['isbn'] = "The Book couldn't be created.";
                    }
                }
                break;
            case 'fullForm':
                $errors =  BookCreationChecker::checkCorrectForm($data, $this->bookRepository, $errors);
                if (empty($errors)) {
                    $book = $this->bookRepository->generateBook($data);
                    $this->bookRepository->createBook($book);
                }
                break;
        }
        return $errors;
    }


}
<?php

namespace Project\Bookworm\Controller;

use DateTime;
use Project\Bookworm\Model\Book;
use Project\Bookworm\Model\BookRepository;

use Project\Bookworm\Utils\BookCreationChecker;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class CatalogueController
{
    private Twig $twig;
    private BookRepository $bookRepository;
    private Messages $flash;


    public function __construct(Twig $twig, BookRepository $bookRepository, Messages $flash)
    {
        $this->twig = $twig;
        $this->bookRepository = $bookRepository;
        $this->flash = $flash;

    }

    public function showCatalogue(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $books = $this->bookRepository->fetchAllBooks();

        return $this->twig->render($response, 'catalogue.twig',  [
            'formAction' => $routeParser->urlFor("catalogue"),
            'formMethod' => "POST",
            'books' => $books
        ]);
    }

    private function createBookFromISBN(string $ISBN)
    {
        //TODO dasdad
    }

    public function handleFormSubmission(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $errors = $this->validateForms($data);
        //$errors['isbn'] = 'The ISBN code is not valid.';
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

    private function findBookByISBN($isbn) {


        return new Book( 100,
            "el mago",
            "Marc Sabater",
            "no description",
            400,
            "www.google.com",
            new DateTime(""),
            new DateTime("")
            );
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
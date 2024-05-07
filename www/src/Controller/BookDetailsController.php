<?php

namespace Project\Bookworm\Controller;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Project\Bookworm\Model\BookRepository;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class BookDetailsController
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

    public function showBookDetails(Request $request, Response $response, array $args): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $bookId = $args['id'];
        $book = $this->bookRepository->findBookById($bookId);

        if ($book === null) {
            // Handle case when book with provided id is not found
            // For example, return a 404 page
            return $response->withStatus(404);
        }

        $bookAverageRating = $this->bookRepository->getAverageRating($bookId);
        $bookReviews = $this->bookRepository->getBookReviews($bookId);

        // para cargar la imagen del libro!
        if (str_starts_with($book->getCoverImage(), "file_")) {
            $book->addPathToCoverImage("/uploads/");
        }

        return $this->twig->render($response, 'bookDetails.twig', [
            'book' => $book,
            'rating' => $bookAverageRating,
            'reviews' => $bookReviews,
            'formErrors' => "",
            'formData' => "",
            'formAction' => $routeParser->urlFor("bookDetail",  ['id' => $bookId]),
            'formMethod' => "GET"
        ]);
    }

    public function rateBook(int $rating) {
        // TODO Marcos, aqui hay que hacer la logica de hacer rating del libro
    }



}

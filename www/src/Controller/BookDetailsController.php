<?php

namespace Project\Bookworm\Controller;

use GuzzleHttp\Client;
use Project\Bookworm\Model\User;
use Project\Bookworm\Model\UserRepository;
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
    private string $username;
    private UserRepository $userRepository;

    private string $profile_photo;
    private User $user;
    private FlashController $flashController;

    public function __construct(Twig $twig, BookRepository $bookRepository, UserRepository $userRepository, FlashController $flashController, Messages $flash)
    {
        $this->twig = $twig;

        $this->bookRepository = $bookRepository;
        $this->userRepository = $userRepository;
        $this->flashController = $flashController;

        $this->flash = $flash;
        $this->client = new Client();

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
                return 0;
            }
        }

        return -2;
    }


    public function showBookDetails(Request $request, Response $response, array $args): Response
    {
        $session_result = $this->checkSession();
        if ($session_result == -1 ){
            return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the landing page.')->withStatus(302);
        }
        if ($session_result == -2) {
            $message = 'You must be logged in to access the user profile page.';
            return $this->flashController->redirectToSignIn($request, $response, $message)->withStatus(302);
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $bookId = $args['id'];
        $book = $this->bookRepository->findBookById($bookId);

        if ($book === null) {
            // Handle case when book with provided id is not found
            // For example, return a 404 page
            return $response->withStatus(404);
        }

        $numberOfReviews = $this->bookRepository->countReviews($bookId);
        $averageRating = $this->bookRepository->averageRating($bookId);

        if (str_starts_with($book->getCoverImage(), "file_")) {
            $book->addPathToCoverImage("/uploads/");
        }

        return $this->twig->render($response, 'bookDetails.twig', [
            'book' => $book,
            'rating' => $numberOfReviews,
            'reviews' => $averageRating,
            'formErrors' => "",
            'formData' => "",
            'formAction' => $routeParser->urlFor("bookDetail",  ['id' => $bookId]),
            'formMethod' => "GET",
            'session' => $_SESSION['email'] ?? [],
            'photo' => $this->profile_photo
        ]);
    }

    public function rateBook(int $rating) {
        // TODO Marcos, aqui hay que hacer la logica de hacer rating del libro
    }

    public function showBookReviews(Request $request, Response $response, array $args): Response
    {
        // Obtener el ID del libro de los argumentos de la ruta
        $bookId = $args['id'];

        // Lógica para obtener las revisiones del libro
        $reviews = $this->bookRepository->getBookReviews(70);


        // Renderizar el template con las revisiones y otros datos necesarios
        return $this->twig->render($response, 'book_reviews.twig', [
            'bookId' => $bookId,
            'reviews' => $reviews,
        ]);
    }


}

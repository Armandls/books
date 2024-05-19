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
use function DI\get;
use function DI\value;

class BookDetailsController
{
    private Twig $twig;
    private BookRepository $bookRepository;
    private Messages $flash;
    private $client;
    private UserRepository $userRepository;
    private FlashController $flashController;

    public function __construct(Twig $twig, BookRepository $bookRepository, UserRepository $userRepository, FlashController $flashController, Messages $flash)
    {
        $this->twig = $twig;

        $this->bookRepository = $bookRepository;
        $this->userRepository = $userRepository;
        $this->flashController = $flashController;

        $this->flash = $flash;
        $this->client = new Client();
    }

    public function showBookDetails(Request $request, Response $response, array $args): Response
    {

        if (isset($_SESSION['email'])) {
            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $profile_photo = "/uploads/{$user->profile_picture()}";
            $username = $user->username();

            if ($username == null)  {
                return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the Book Details.')->withStatus(302);
            }
            else {
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                $bookId = $args['id'];
                $book = $this->bookRepository->findBookById($bookId);

                if ($book === null) {
                    return $response->withStatus(404);
                }

                $numberOfReviews = $this->bookRepository->countReviews($bookId);
                $averageRating = $this->bookRepository->averageRating($bookId);
                $numberOfRatings = $this->bookRepository->countRaiting($bookId);
                $reviews = $this->bookRepository->getBookReviews($bookId);

                if (str_starts_with($book->getCoverImage(), "file_")) {
                    $book->addPathToCoverImage("/uploads/");
                }

                $errors = [];

                return $this->twig->render($response, 'bookDetails.twig', [
                    'book' => $book,
                    'rating' => $averageRating,
                    'reviews' => $numberOfReviews,
                    'numRaiting' => $numberOfRatings,
                    'arrayReviews' => $reviews,
                    'errors' => $errors,
                    'session' => $_SESSION['email'] ?? [],
                    'photo' => $profile_photo
                ]);
            }
        }
        else {
            return $this->flashController->redirectToSignIn($request, $response, 'You must be logged in to access the Book Details.')->withStatus(302);
        }
    }



    public function deleteReview(Request $request, Response $response, array $args): Response
    {
        if (isset($_SESSION['email'])) {
            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $profile_photo = "/uploads/{$user->profile_picture()}";
            $username = $user->username();

            if ($username == null)  {
                return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the Book Details.')->withStatus(302);
            }
            else {
                $bookId = $args['id'];

                $userId = $this->userRepository->findByEmail($_SESSION['email'])->id();

                $reviewDeleted = $this->bookRepository->deleteReviewById($userId, $bookId);
                if ($reviewDeleted) {
                    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                    return $response->withHeader('Location', $routeParser->urlFor("bookDetail", ['id' => $bookId]));
                } else {
                    $errors = [];
                    $errors['deleteReview'] = 'Error deleting the review.';

                    $response->getBody()->write(json_encode($errors));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(401);

                }

            }
        }
        else {
            return $this->flashController->redirectToSignIn($request, $response, 'You must be logged in to access the Book Details.')->withStatus(302);
        }
    }

    public function deleteRating(Request $request, Response $response, array $args): Response
    {
        if (isset($_SESSION['email'])) {
            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $profile_photo = "/uploads/{$user->profile_picture()}";
            $username = $user->username();

            if ($username == null)  {
                return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the Book Details.')->withStatus(302);
            }
            else {
                $bookId = $args['id'];

                $userId = $this->userRepository->findByEmail($_SESSION['email'])->id();

                $ratingDeleted = $this->bookRepository->deleteRatingById($userId, $bookId);

                // Crear una nueva respuesta con la redirección
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                return $response->withHeader('Location', $routeParser->urlFor("bookDetail", ['id' => $bookId]));
            }
        }
        else {
            return $this->flashController->redirectToSignIn($request, $response, 'You must be logged in to access the Book Details.')->withStatus(302);
        }
    }


    public function addReview(Request $request, Response $response, array $args): Response
    {
        if (isset($_SESSION['email'])) {
            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $profile_photo = "/uploads/{$user->profile_picture()}";
            $username = $user->username();

            if ($username == null) {
                return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the Book Details.')->withStatus(302);
            } else {
                $bookId = $args['id'];
                $userId = $user->id();

                // Obtiene los datos del formulario
                $data = $request->getParsedBody();

                // Validación de los datos del formulario (si es necesario)
                if (empty($data['review_text'])) {
                    $errors = [];
                    $errors['addReview'] = 'Review text cannot be empty.';

                    $response->getBody()->write(json_encode($errors));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }

                // Comprueba si el usuario ya ha revisado este libro
                if ($this->bookRepository->hasUserReviewedBook($userId, $bookId)) {
                    $errors = [];
                    $errors['addReview'] = 'You have already reviewed this book.';

                    $response->getBody()->write(json_encode($errors));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }

                // Inserta la revisión en la base de datos utilizando tu método existente
                $reviewText = $data["review_text"];
                $reviewAdded = $this->bookRepository->addReview($userId, $bookId, $reviewText);

                if ($reviewAdded) {
                    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                    return $response->withHeader('Location', $routeParser->urlFor("bookDetail", ['id' => $bookId]))->withStatus(302);
                } else {
                    $errors = [];
                    $errors['addReview'] = 'Error adding the review.';

                    $response->getBody()->write(json_encode($errors));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
                }
            }
        } else {
            return $this->flashController->redirectToSignIn($request, $response, 'You must be logged in to access the Book Details.')->withStatus(302);
        }
    }




    public function addBookRating(Request $request, Response $response, array $args): Response
    {
        if (isset($_SESSION['email'])) {
            $user = $this->userRepository->findByEmail($_SESSION['email']);
            $profile_photo = "/uploads/{$user->profile_picture()}";
            $username = $user->username();

            if ($username == null) {
                return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the Book Details.')->withStatus(302);
            } else {
                $bookId = $args['id'];
                $userId = $this->userRepository->findByEmail($_SESSION['email'])->id();

                // Obtenemos el rating enviado en la solicitud
                $data = $request->getParsedBody();
                $rating = $data['rating'];

                $ratingAdded = $this->bookRepository->addRatingToBook($userId, $bookId, $rating);

                // Redirigimos de vuelta a la página de detalles del libro después de agregar el rating
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                return $response->withHeader('Location', $routeParser->urlFor("bookDetail", ['id' => $bookId]));
            }
        }
        else {
            return $this->flashController->redirectToSignIn($request, $response, 'You must be logged in to access the Book Details.')->withStatus(302);
        }
    }



}

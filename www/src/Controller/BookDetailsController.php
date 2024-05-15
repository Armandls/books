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
    private ?string $username;
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

                return $this->twig->render($response, 'bookDetails.twig', [
                    'book' => $book,
                    'rating' => $averageRating,
                    'reviews' => $numberOfReviews,
                    'numRaiting' => $numberOfRatings,
                    'arrayReviews' => $reviews,
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

                $this->bookRepository->deleteReviewById($userId, $bookId);

                // Crear una nueva respuesta con la redirección
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                return $response->withHeader('Location', $routeParser->urlFor("bookDetail", ['id' => $bookId]));
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

                $this->bookRepository->deleteRatingById($userId, $bookId);

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
                $userId = $this->userRepository->findByEmail($_SESSION['email'])->id();

                // Obtiene los datos del formulario
                $data = $request->getParsedBody();

                // Validación de los datos del formulario (si es necesario)

                // Inserta la revisión en la base de datos utilizando tu método existente
                // Suponiendo que el texto de la revisión está en el campo 'review_text' del formulario
                $reviewText = $data["review_text"];


                $this->bookRepository->addReview($userId, $bookId, $reviewText);

                // Redirige de vuelta a la página de detalles del libro después de agregar la revisión
                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                return $response->withHeader('Location', $routeParser->urlFor("bookDetail", ['id' => $bookId]));
            }
        }
        else {
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

                $this->bookRepository->addRatingToBook($userId, $bookId, $rating);

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

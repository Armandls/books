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
            'formErrors' => "",
            'formData' => "",
            'formAction' => $routeParser->urlFor("bookDetail",  ['id' => $bookId]),

            'formMethod' => "GET",
            'session' => $_SESSION['email'] ?? [],
            'photo' => $this->profile_photo
        ]);
    }



    public function deleteReview(Request $request, Response $response, array $args): Response
    {
        $session_result = $this->checkSession();
        if ($session_result == -1 ){
            return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the landing page.')->withStatus(302);
        }
        if ($session_result == -2) {
            $message = 'You must be logged in to access the user profile page.';
            return $this->flashController->redirectToSignIn($request, $response, $message)->withStatus(302);
        }
        $bookId = $args['id'];

        $userId = 1;

        $this->bookRepository->deleteReviewById(1, $bookId);

        // Crear una nueva respuesta con la redirección
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response->withHeader('Location', $routeParser->urlFor("bookDetail", ['id' => $bookId]));
    }

    public function deleteRating(Request $request, Response $response, array $args): Response
    {

        $session_result = $this->checkSession();
        if ($session_result == -1 ){
            return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the landing page.')->withStatus(302);
        }
        if ($session_result == -2) {
            $message = 'You must be logged in to access the user profile page.';
            return $this->flashController->redirectToSignIn($request, $response, $message)->withStatus(302);
        }
        $bookId = $args['id'];

        $userId = 1;

        $this->bookRepository->deleteRatingById(1, $bookId);

        // Crear una nueva respuesta con la redirección
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response->withHeader('Location', $routeParser->urlFor("bookDetail", ['id' => $bookId]));
    }


public function addReview(Request $request, Response $response, array $args): Response
{

    $session_result = $this->checkSession();
    if ($session_result == -1 ){
        return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the landing page.')->withStatus(302);
    }
    if ($session_result == -2) {
        $message = 'You must be logged in to access the user profile page.';
        return $this->flashController->redirectToSignIn($request, $response, $message)->withStatus(302);
    }
    $bookId = $args['id'];
    $userId = 1; // Este es un valor de ejemplo, deberías obtener el ID del usuario de la sesión

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


public function addBookRating(Request $request, Response $response, array $args): Response
{

    $session_result = $this->checkSession();
    if ($session_result == -1 ){
        return $this->flashController->redirectToUserProfile($request, $response, 'You must complete your profile to access the landing page.')->withStatus(302);
    }
    if ($session_result == -2) {
        $message = 'You must be logged in to access the user profile page.';
        return $this->flashController->redirectToSignIn($request, $response, $message)->withStatus(302);
    }

    $bookId = $args['id'];
    $userId = 1;

    // Obtenemos el rating enviado en la solicitud
    $data = $request->getParsedBody();
    $rating = $data['rating'];

    $this->bookRepository->addRatingToBook($userId, $bookId, $rating);

    // Redirigimos de vuelta a la página de detalles del libro después de agregar el rating
    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
    return $response->withHeader('Location', $routeParser->urlFor("bookDetail", ['id' => $bookId]));
}



}

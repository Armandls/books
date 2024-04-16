<?php
namespace Salle\LSCryptoNews\Controller;

use Salle\LSCryptoNews\Model\UserRepository;
use Salle\LSCryptoNews\Utils\LoginFormChecker;
use Salle\LSCryptoNews\Utils\RegisterFormChecker;
use Slim\Flash\Messages;
use Slim\Views\Twig;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
final class RegisterController
{
    private Twig $twig;
    private UserRepository $repository;
    private Messages $flash;

    // You can also use https://stitcher.io/blog/constructor-promotion-in-php-8
    public function __construct(Twig $twig, UserRepository $repository, Messages $flash) {
        $this->twig = $twig;
        $this->repository = $repository;
        $this->flash = $flash;
    }

    public function apply(Request $request, Response $response): Response {

        $messages = $this->flash->getMessages();
        $notifications = $messages['notifications'] ?? [];

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render($response, 'sign-up.twig',
            [
                'formAction' => $routeParser->urlFor("sign-up"),
                'formMethod' => "POST",
                'notifs' => $notifications
            ]
        );
    }

    public function checkFormSubmit(Request $request, Response $response): Response {
        $formData = $request->getParsedBody();
        $errors = RegisterFormChecker::checkCorrectForm($formData, $this->repository);

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();



        if (empty($errors)) {
            //$_SESSION['email'] = $formData["email"];
            $this->repository->registerNewUser($formData["email"], $formData["password"], intval($formData["numBitcoins"]));

            //setcookie('email', $formData["email"], time() + (86400), "/"); // 1 day expiration
            //setcookie('password', $formData["password"], time() + (86400 ), "/");
            //setcookie('numBitcoins', (string)$formData["numBitcoins"], time() + (86400), "/");

            return $response->withHeader('Location', $routeParser->urlFor("sign-in"));
        } else {
            return $this->twig->render($response, 'sign-up.twig',
                [
                    'formErrors' => $errors,
                    'formData' => $formData,
                    'formAction' => $routeParser->urlFor("sign-up"),
                    'formMethod' => "POST"
                ]
            );
        }
    }



}
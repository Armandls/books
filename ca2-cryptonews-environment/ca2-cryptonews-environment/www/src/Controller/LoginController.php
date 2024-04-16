<?php
declare(strict_types=1);
namespace Salle\LSCryptoNews\Controller;

use Salle\LSCryptoNews\Model\UserRepository;
use Salle\LSCryptoNews\Utils\LoginFormChecker;
use Slim\Flash\Messages;
use Slim\Views\Twig;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
final class LoginController
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

        return $this->twig->render(
            $response,
            'sign-in.twig',
            [
                'formAction' => $routeParser->urlFor("sign-in"),
                'formMethod' => "POST",
                'notifications' => $notifications

            ]
        );
    }

    public function checkLoginSubmit(Request $request, Response $response): Response {
        $formData = $request->getParsedBody();

        $errors = LoginFormChecker:: checkLoginForm($formData, $this->repository);

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if (empty($errors)) {
            $_SESSION['email'] = $formData["email"];

            $user = LoginFormChecker::getUser($formData, $this->repository);
            if (!empty($user))
            {
                setcookie('email', $user->getEmail(), time() + (86400), "/"); // 1 day expiration
                setcookie('password', $user->getPassword(), time() + (86400 ), "/");
                setcookie('numBitcoins', (string)$user->getCoins(), time() + (86400), "/");
                return $response->withHeader('Location', $routeParser->urlFor('home'));

            } else {
                return $this->twig->render(
                    $response,
                    'sign-in.twig',
                    [
                        'formErrors' => $errors,
                        'formData' => $formData,
                        'formAction' => $routeParser->urlFor("sign-in"),
                        'formMethod' => "POST"
                    ]
                );
            }

        } else {
            return $this->twig->render(
                $response,
                'sign-in.twig',
                [
                    'formErrors' => $errors,
                    'formData' => $formData,
                    'formAction' => $routeParser->urlFor("sign-in"),
                    'formMethod' => "POST"
                ]
            );
        }
    }

}
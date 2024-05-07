<?php

declare(strict_types=1);

namespace Project\Bookworm\Controller;

use http\Message;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class FlashController
{

    private Twig $twig;
    private Messages $flash;

    public function __construct(
        Twig $twig,
        Messages $flash
    ) {
        $this->twig = $twig;
        $this->flash = $flash;
    }

    public function redirectToSignIn (Request $request, Response $response, string $message): Response {

        $this->flash->addMessage('flash', $message);

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $url = $routeParser->urlFor("get-sign-in");

        // Redirige al usuario a la página de inicio de sesión
        return $response
            ->withHeader('Location', $url)->withStatus(302);
    }

    public function redirectToUserProfile (Request $request, Response $response, string $message): Response {

        $this->flash->addMessage('flash', $message);

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $url = $routeParser->urlFor("show-profile");

        // Redirige al usuario a la página de inicio de sesión
        return $response
            ->withHeader('Location', $url)->withStatus(302);
    }
}
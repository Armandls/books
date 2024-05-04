<?php

namespace Project\Bookworm\Controller;


use Project\Bookworm\Model\BookRepository;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
require __DIR__ . '/../../vendor/autoload.php';

class ForumsController
{
    private Twig $twig;
    private Messages $flash;

    public function __construct(Twig $twig, Messages $flash)
    {
        $this->twig = $twig;
        $this->flash = $flash;


    }

    public function showCurrentForums(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render($response, 'forums.twig',  [
            'formAction' => $routeParser->urlFor("forums"),
            'formMethod' => "GET",
        ]);
    }

}
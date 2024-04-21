<?php

namespace Project\Bookworm\Controller;

use Project\Bookworm\Model\BookRepository;
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
        // Renderizar la plantilla de landing


        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render($response, 'catalogue.twig',  [
            'formAction' => $routeParser->urlFor("catalogue"),
            'formMethod' => "GET",
        ]);
    }

}
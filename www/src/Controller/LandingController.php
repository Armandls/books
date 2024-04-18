<?php

namespace Project\Bookworm\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class LandingController
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function apply(Request $request, Response $response): Response
    {
        // Renderizar la plantilla de landing
        return $this->twig->render($response, 'landing.twig', []);
    }

}
<?php
declare(strict_types=1);

namespace Salle\LSCryptoNews\Controller;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Flash\Messages;
use Slim\Views\Twig;

final class HomeController
{
    private Twig $twig;

    private Messages $flash;

    // You can also use https://stitcher.io/blog/constructor-promotion-in-php-8
    public function __construct(Twig $twig, Messages $flash)
    {
        $this->twig = $twig;
        $this->flash = $flash;
    }

    public function apply(Request $request, Response $response): Response
    {
        $username = $request->getCookieParams()['email'] ?? "";

        if ($username == "") {
            $username = "stranger";
        }   else {
            $username = explode('@', $username)[0];
        }

        return $this->twig->render($response, 'home.twig', ['username' => $username]);
    }
}
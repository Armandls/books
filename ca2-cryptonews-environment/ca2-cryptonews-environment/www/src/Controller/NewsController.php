<?php
declare(strict_types=1);

namespace Salle\LSCryptoNews\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\LSCryptoNews\Utils\NewsFiles;
use Slim\Flash\Messages;
use Slim\Views\Twig;

class NewsController
{
    private Twig $twig;
    private Messages $flash;


    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function apply(Request $request, Response $response): Response
    {
        $articles = NewsFiles::getAllArticles();

        return $this->twig->render(
            $response,
            'news.twig',
            [
                'articles' => $articles,
                'formMethod' => "GET"
            ]
        );
    }
}

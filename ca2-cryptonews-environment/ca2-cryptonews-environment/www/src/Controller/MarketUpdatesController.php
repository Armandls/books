<?php
declare(strict_types=1);

namespace Salle\LSCryptoNews\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\LSCryptoNews\Utils\MarketPrices;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class MarketUpdatesController
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function apply(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $pageTitle = "Welcome to CryptoNews! Login if you want to see your updated data.";
        $cryptoBalance = "";
        $sessionEmpty = empty($_SESSION["email"]);

        if (!$sessionEmpty) {
            $pageTitle = "Market Updates";
            $cryptoBalance = $request->getCookieParams()['numBitcoins'] ?? 0;
        }

        $marketPrices = MarketPrices::getAll();

        return $this->twig->render(
            $response,
            'mkt.twig',
            [
                'pageTitle' => $pageTitle,
                'cryptoBalance' => $cryptoBalance,
                'marketPrices' => $marketPrices,
                'sessionEmpty' => $sessionEmpty
            ]
        );
    }
}

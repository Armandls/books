<?php
declare(strict_types=1);
namespace Salle\LSCryptoNews\Controller;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
final class CookieMonsterController
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function showAdvice(Request $request, Response $response): Response
    {
        $cookie = FigRequestCookies::get($request, 'cookies_advice', "0");

        $isAdvised = boolval($cookie->getValue());

        if (!$isAdvised) {
            $response = FigResponseCookies::set(
                $response,
                $this->generateAdviceCookie()
            );
        }

        return $this->twig->render($response, 'cookies_advice.twig', [
            'isAdvised' => $isAdvised,
        ]);
    }

    private function generateAdviceCookie(): SetCookie
    {
        return SetCookie::create('cookies_advice')
            ->withValue("1")
            ->withDomain('localhost')
            ->withPath('/cookies');
    }
}
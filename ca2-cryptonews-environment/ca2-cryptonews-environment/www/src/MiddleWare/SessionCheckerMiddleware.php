<?php
declare(strict_types=1);

namespace Salle\LSCryptoNews\MiddleWare;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Flash\Messages;

final class SessionCheckerMiddleware
{
    private Messages $flash;

    public function __construct(Messages $flash)
    {
        $this->flash = $flash;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        $target = $request->getRequestTarget();
        $message = "";
        if ($target === "/news") {
            $message = "You must be logged in to access the news page.";
        }

        if (empty($_SESSION['email'])) {
            $flash = new Messages();
            $flash->addMessage('notifications', $message);
            return $response->withHeader('Location', '/sign-in');
        }

        return $handler->handle($request);
    }
}

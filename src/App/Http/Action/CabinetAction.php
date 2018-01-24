<?php

namespace App\Http\Action;

use App\Http\Middleware\BasicAuthMiddleware;
use PHPUnit\Framework\MockObject\RuntimeException;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

class CabinetAction
{

    public function __invoke(ServerRequestInterface $request)
    {
        $username = $request->getAttribute(BasicAuthMiddleware::ATTRIBUTE);

        return new HtmlResponse("You are logged in as " . $username);
    }
}
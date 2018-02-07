<?php

namespace Framework\Http\Pipeline;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class InteropHandlerWrapper implements RequestHandlerInterface
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * Handle the request and return a response.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->callback)($request);
    }
}
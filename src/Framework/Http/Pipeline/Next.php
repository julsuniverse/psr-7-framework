<?php

namespace Framework\Http\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Next
{
    private $default;
    private $queue;
    public function __construct(\SplQueue $queue, callable $default)
    {
        $this->default = $default;
        $this->queue = $queue;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if($this->queue->isEmpty())
            return ($this->default)($request, $response);

        $current = $this->queue->dequeue();

        return $current($request, $response, function(ServerRequestInterface $request) use($response) {
            return $this($request, $response);
        });
    }
}
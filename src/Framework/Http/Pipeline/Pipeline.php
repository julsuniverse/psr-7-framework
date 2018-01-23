<?php

namespace Framework\Http\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Pipeline
{
    private $queue;

    public function __construct()
    {
        $this->queue = new \SplQueue();
    }

    public function pipe(callable $middleware): void
    {
        $this->queue->enqueue($middleware);
    }

    public function __invoke(ServerRequestInterface $request, callable $default): ResponseInterface
    {
        return $this->next($request, $default);
    }

    private function next(ServerRequestInterface $request, callable $default)
    {
        if($this->queue->isEmpty())
            return $default($request);

        $current = $this->queue->dequeue();

        return $current($request, function(ServerRequestInterface $request) use ($default) {
            return $this->next($request, $default);
        });
    }
}
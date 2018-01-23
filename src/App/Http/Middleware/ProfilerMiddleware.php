<?php

namespace App\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class ProfilerMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable  $next)
    {
        $start = microtime(true);

        /** @var ServerRequestInterface $response */
        $response = $next($request);

        $stop = microtime(true);

        return $response->withHeader('X-Profiler', $stop - $start);
    }
}
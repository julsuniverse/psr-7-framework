<?php

namespace Tests\Framework\Http\Pipeline;

use Framework\Http\Pipeline\Pipeline;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ServerRequest;

class PipelineTest extends TestCase
{
    public function testPipe()
    {
        $pipeline = new Pipeline();

        $pipeline->pipe(new Middleware1);
        $pipeline->pipe(new Middleware2);

        $response = $pipeline(new ServerRequest(), new Last());

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'middleware1' => 1,
                'middleware2' => 2
            ]),
            $response->getBody()->getContents()
        );
    }
}

class Middleware1
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        return $next($request->withAttribute('middleware1', 1));
    }
}

class Middleware2
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        return $next($request->withAttribute('middleware2', 2));
    }
}

class Last
{
    public function __invoke(ServerRequestInterface $request)
    {
        return new JsonResponse($request->getAttributes());
    }
}
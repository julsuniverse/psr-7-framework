<?php

namespace Framework\Http\Middleware;


use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Router\Exception\RequestNotMatchedException;
use Framework\Http\Router\Result;
use Framework\Http\Router\Router;
use Psr\Http\Message\ServerRequestInterface;

class RouteMiddleware
{
    private $router;
    private $resolver;

    public function __construct(Router $router, MiddlewareResolver $resolver)
    {
        $this->router = $router;
        $this->resolver = $resolver;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            $result = $this->router->match($request); //проверяем соответвие запроса с нашими роутерами
            foreach($result->getAttributes() as $attribute => $value) { //достаем из запроса атрибуты (в запросе кроме аттрибутов еще куча другой информации!)
                $request = $request->withAttribute($attribute, $value);
            }
            return $next($request->withAttribute(Result::class, $result));

        } catch (RequestNotMatchedException $e) {
            return $next($request);
        }
    }

}
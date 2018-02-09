<?php

use App\Http\Middleware\BasicAuthMiddleware;
use App\Http\Middleware\ErrorHandlerMiddleware;
use Framework\Container\Container;
use Framework\Http\Application;
use App\Http\Middleware;
use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Router\AuraRouterAdapter;
use Framework\Http\Router\Router;
use Zend\Diactoros\Response;

return [
    Application::class => function (Container $container) {
        return new Application(
            $container->get(MiddlewareResolver::class),
            $container->get(Router::class),
            new  Middleware\NotFoundHandler(),
            new Response()
        );
    },

    Router::class => function () {
        return new AuraRouterAdapter(new Aura\Router\RouterContainer());  //Оборачиваем AuraRouter в свой адаптер
    },

    MiddlewareResolver::class => function (Container $container) {
        return new MiddlewareResolver($container);
    },

    BasicAuthMiddleware::class => function (Container $container) {
        return new BasicAuthMiddleware($container->get('config')['users']);
    },

    ErrorHandlerMiddleware::class => function (Container $container) {
        return new ErrorHandlerMiddleware($container->get('config')['debug']);
    },

];
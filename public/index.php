<?php

use App\Http\Action;
use App\Http\Action\CabinetAction;
use App\Http\Middleware;
use App\Http\Middleware\BasicAuthMiddleware;
use App\Http\Middleware\ProfilerMiddleware;
use Framework\Http\Middleware\DispatchMiddleware;
use Framework\Http\Middleware\RouteMiddleware;
use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Pipeline\Pipeline;
use Framework\Http\Router\AuraRouterAdapter;
use Framework\Http\Router\Exception\RequestNotMatchedException;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

### Initialization

$params = [
    'users' => ['admin' => 'password'],
    'debug' => true,
];

$aura = new Aura\Router\RouterContainer();
$routes = $aura->getMap();

$routes->get('home', '/', Action\HelloAction::class);
$routes->get('about', '/about', Action\AboutAction::class);

/*
 * Вместо action (handler) мы передаем анонимную функцию, которая по цепочке будет вызывать сначала middleware и в конце нужный нам экшен.
 * Нужный экшен будет замыкать цепочку вызовов.
 */

$routes->get('cabinet', '/cabinet', [
    new BasicAuthMiddleware($params['users']),
    CabinetAction::class,
]);

$routes->get('blog', '/blog', Action\Blog\IndexAction::class);
$routes->get('blog_show', '/blog/{id}', Action\Blog\ShowAction::class)->tokens(['id' => '\d+']);


$router = new AuraRouterAdapter($aura); //Оборачиваем AuraRouter в свой адаптер
$resolver = new MiddlewareResolver();
//$app = new \Framework\Http\Application($resolver, new Middleware\NotFoundHandler()); //создаем объект Pipeline
$app = new \Zend\Stratigility\MiddlewarePipe();

/*$app->pipe(
    new \Zend\Stratigility\Middleware\CallableMiddlewareDecorator(
        $resolver->resolve(
            new Middleware\ErrorHandlerMiddleware($params['debug'])
        )
    )
);*/

//$app->pipe(Middleware\CredentialsMiddleware::class);
//$app->pipe(ProfilerMiddleware::class);
//$app->pipe(new \Zend\Stratigility\Middleware\CallableMiddlewareDecorator(new RouteMiddleware($router, $resolver)));

//$app->pipe(new \Zend\Stratigility\Middleware\CallableMiddlewareDecorator(new DispatchMiddleware($resolver)));
//print_r($app);
### Running

$request = ServerRequestFactory::fromGlobals(); //содержит все информацию о запросе
//$response = $app->run($request, new \Zend\Diactoros\Response()); //запускает трубопровод со всеми middleware

$response = $app->process($request, new Middleware\NotFoundHandler);

### Sending
//отправляем обратно в бразуер результат
$emitter = new SapiEmitter();
$emitter->emit($response);
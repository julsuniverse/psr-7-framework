<?php

use App\Http\Action;
use App\Http\Action\CabinetAction;
use App\Http\Middleware;
use App\Http\Middleware\BasicAuthMiddleware;
use App\Http\Middleware\ProfilerMiddleware;
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
$pipeline = new Pipeline(); //создаем объект Pipeline

$pipeline->pipe($resolver->resolve(ProfilerMiddleware::class)); //middleware будет выполняться всегдапше

### Running

$request = ServerRequestFactory::fromGlobals(); //содержит все информацию о запросе
try {
    $result = $router->match($request); //проверяем соответвие запроса с нашими роутерами
    foreach($result->getAttributes() as $attribute => $value) { //достаем из запроса атрибуты (в запросе кроме аттрибутов еще куча другой информации!)
        $request = $request->withAttribute($attribute, $value);
    }

    $handler = $result->getHandler(); //получаем обработчик из роутера (указываем при формировании роута)
    $pipeline->pipe($resolver->resolve($handler));

} catch (RequestNotMatchedException $e) {}

$response = $pipeline($request, new Middleware\NotFoundHandler()); //запускает трубопровод со всеми middleware
### Postprocessing

$response = $response->withHeader('X-Developer', 'Julia');

### Sending
//отправляем обратно в бразуер результат
$emitter = new SapiEmitter();
$emitter->emit($response);
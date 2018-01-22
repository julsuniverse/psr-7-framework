<?php

use App\Http\Action;
use Framework\Http\ActionResolver;
use Framework\Http\Router\AuraRouterAdapter;
use Framework\Http\Router\Exception\RequestNotMatchedException;
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
$map = $aura->getMap();

$map->get('home', '/', Action\HelloAction::class);
$map->get('about', '/about', Action\AboutAction::class);
$map->get('cabinet', '/cabinet', new Action\CabinetAction($params['users']));
$map->get('blog', '/blog', Action\Blog\IndexAction::class);
$map->get('blog_show', '/blog/{id}', Action\Blog\ShowAction::class)->tokens(['id' => '\d+']);


$router = new AuraRouterAdapter($aura); //Оборачиваем AuraRouter в свой адаптер
$resolver = new ActionResolver();

### Running

$request = ServerRequestFactory::fromGlobals(); //содержит все информацию о запросе
try {
    $result = $router->match($request); //проверяем соответвие запроса с нашими роутерами
    foreach($result->getAttributes() as $attribute => $value) { //достаем из запроса атрибуты (в запросе кроме аттрибутов еще куча другой информации!)
        $request = $request->withAttribute($attribute, $value);
    }

    $action = $resolver->resolve($result->getHandler()); //получаем обработчик запроса (экшен)
    $response = $action($request); //передаем в экшен запрос с аттрибутами, если они есть
} catch (RequestNotMatchedException $e) {
    $response = new HtmlResponse('Undefined page', 404);
}

### Postprocessing

$response = $response->withHeader('X-Developer', 'Julia');

### Sending
//отправляем обратно в бразуер результат
$emitter = new SapiEmitter();
$emitter->emit($response);
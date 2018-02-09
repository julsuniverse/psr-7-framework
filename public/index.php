<?php

use Psr\Container\ContainerInterface;
use Framework\Http\Application;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

/** @var Application $app */
/** @var ContainerInterface $container */

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

require 'config/container.php';
$app = $container->get(Application::class);

require 'config/pipeline.php';
require 'config/routes.php';

$request = ServerRequestFactory::fromGlobals(); //содержит все информацию о запросе
$response = $app->run($request, new Response());

//отправляем обратно в бразуер результат
$emitter = new SapiEmitter();
$emitter->emit($response);
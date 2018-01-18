<?php
/**
 * Created by PhpStorm.
 * User: Yulja
 * Date: 18.01.18
 * Time: 17:40
 */

namespace Framework\Http\Router\Exception;


use Psr\Http\Message\ServerRequestInterface;

class RequestNotMatchedException extends \LogicException
{
    private $request;

    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct('Matches not found');
        $this->request = $request;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
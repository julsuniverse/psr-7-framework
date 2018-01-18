<?php

namespace Framework\Http\Router\Exception;


use Throwable;

class RouteNotFoundException extends \LogicException
{
    private $name;
    private $params;

    public function __construct($name, array $params, Throwable $previous = null)
    {
        parent::__construct('Route "' . $name . '" not found.', $previous);
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
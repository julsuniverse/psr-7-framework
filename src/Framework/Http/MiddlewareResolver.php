<?php

namespace Framework\Http;


class MiddlewareResolver
{
    public function resolve($handler): callable
    {
        return \is_string($handler) ? new $handler() : $handler;
    }
}
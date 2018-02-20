<?php

namespace Tests\App\Http\Action;

use App\Http\Action\HelloAction;
use Framework\Template\TemplateRenderer;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;

class HelloActionTest extends TestCase
{
    public function TestGuest()
    {
        $action = new HelloAction(new TemplateRenderer('templates'));

        $request = new ServerRequest();
        $response = $action($request);

        self::assertEquals(200, $response->getStatusCode());
        self::assertContains('Hello, Guest!', $response->getBody()->getContents());
    }

    public function testJohn()
    {
        $action = new HelloAction(new TemplateRenderer('templates'));

        $request = (new ServerRequest())
            ->withQueryParams(['name' => 'John']);

        $response = $action($request);

        self::assertContains('Hello, John!', $response->getBody()->getContents());
    }
}
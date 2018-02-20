<?php

namespace Tests\App\Http\Action;

use App\Http\Action\HelloAction;
use Framework\Template\PhpRenderer;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;

class HelloActionTest extends TestCase
{
    private $renderer;

    public function setUp(): void
    {
        parent::setUp();
        $this->renderer = new PhpRenderer('templates');
    }

    public function test()
    {
        $action = new HelloAction($this->renderer);
        $response = $action();
        self::assertEquals(200, $response->getStatusCode());
        self::assertContains('Hello!', $response->getBody()->getContents());
    }
}
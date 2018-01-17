<?php

namespace  Tests\Framework\Http;

use Framework\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();

        $_GET = [];
        $_POST = [];
    }

    public function testEmpty(): void
    {
        $request = new Request();

        self::assertEquals([], $request->getQueryParams()); //Проверяем, что метод возвращает пустой массив
        self::assertNull($request->getParsedBody()); //Проверяем, что возвращает пустоту
    }

    public function testQueryParams(): void
    {
        $_GET = $data = [
            'name' => 'John',
            'age' => 25,
        ];

        $request = new Request();

        self::assertEquals($data, $request->getQueryParams()); //Проверяем, что из QueryParams вощзвращается тот же массив, что мы присвоили в data
        self::assertNull($request->getParsedBody());
    }

    public function testParsedBody(): void
    {
        $_POST = $data = ['title' => 'Title'];

        $request = new Request();

        self::assertEquals([], $request->getQueryParams());
        self::assertEquals($data, $request->getParsedBody());

    }
}

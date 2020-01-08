<?php

namespace Tests\Controllers;

use App\Controllers\Telegram;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Tests\TestCase;

class TelegramTest extends TestCase
{
    public function test_it_returns_a_successful_response(): void
    {
        $controller = new Telegram();

        $response = $controller($this->botman, new Response());

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
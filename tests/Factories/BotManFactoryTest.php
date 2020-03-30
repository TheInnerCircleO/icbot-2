<?php

namespace Tests\Factories;

use App\Factories\BotManFactory;
use BotMan\BotMan\BotMan;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class BotManFactoryTest extends TestCase
{
    public function test_it_can_register_the_botman_component(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $botman = (new BotManFactory($this->container, $logger))();

        $this->assertInstanceOf(BotMan::class, $botman);
    }
}
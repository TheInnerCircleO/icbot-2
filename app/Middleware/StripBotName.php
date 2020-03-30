<?php

namespace App\Middleware;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use DI\Container;

class StripBotName implements Received
{
    /** @var Container The application container */
    protected $container;

    /**
     * Create a new StripBotName object.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Handle an incoming message.
     *
     * @param IncomingMessage $message
     * @param callable        $next
     * @param BotMan          $bot
     *
     * @return mixed
     */
    public function received(IncomingMessage $message, $next, BotMan $bot)
    {
        $botName = (string) $this->container->get('bot_name');

        if (preg_match("/^[^@\s]+@{$botName}.*$/", $message->getText())) {
            $canonicalized = preg_replace("/@{$botName}/", '', $message->getText());
            $message->setText($canonicalized);
        }

        return $next($message);
    }
}

<?php

namespace App\Providers;

use App\Commands;
use BotMan\BotMan\BotMan;
use PHLAK\Config\Config;
use PHLAK\Config\Interfaces\ConfigInterface;
use Tightenco\Collect\Support\Collection;

class CommandsProvider
{
    /** @var BotMan The BotMan component */
    protected $botman;

    /** @var ConfigInterface The application config */
    protected $config;

    /**
     * Create a new CommandsProvider object.
     *
     * @param \BotMan\BotMan\BotMan                    $botman
     * @param \PHLAK\Config\Interfaces\ConfigInterface $config
     */
    public function __construct(BotMan $botman, ConfigInterface $config)
    {
        $this->botman = $botman;
        $this->config = $config;
    }

    /**
     * Register chatbot commands.
     *
     * @return void
     */
    public function __invoke(): void
    {
        Collection::make(
            $this->config->get('botman.commands', [])
        )->each(function (string $command, string $pattern) {
            $this->botman->hears($pattern, $command);
        });

        $this->botman->fallback(Commands\Fallback::class);
    }
}

<?php

namespace App\Http\Clients;

use App\Comic;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Tightenco\Collect\Support\Collection;

class DilbertClient
{
    /** @const The date of the earliest comic */
    protected const START_DATE = '1989-04-16';

    /** @var Client The Guzzle HTTP client */
    protected $client;

    /**
     * Create a new DilbertClient object.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->client = new Client(array_replace_recursive([
            'base_uri' => 'https://dilbert.com',
            'connect_timeout' => 5,
            'timeout' => 10,
        ], $config));
    }

    /**
     * Fetch the latest comic.
     *
     * @return \App\Comic
     */
    public function latest(): Comic
    {
        $html = (string) $this->client->get(
            sprintf('strip/%s', Carbon::now('America/Phoenix')->format('Y-m-d'))
        )->getBody();

        return new Comic(
            $this->extractTitle($html),
            $this->extractDescription($html),
            $this->extractImageUrl($html),
            $this->extractSourceUrl($html)
        );
    }

    /**
     * Fetch a comic by date.
     *
     * @param string $date
     *
     * @return \App\Comic
     */
    public function byDate(Carbon $date): Comic
    {
        $html = (string) $this->client->get(
            sprintf('strip/%s', $date->format('Y-m-d'))
        )->getBody();

        return new Comic(
            $this->extractTitle($html),
            $this->extractDescription($html),
            $this->extractImageUrl($html),
            $this->extractSourceUrl($html)
        );
    }

    /**
     * Fetch a random comic.
     *
     * @return \App\Comic
     */
    public function random(): Comic
    {
        $html = (string) $this->client->get(
            sprintf('strip/%s', $this->randomDate()->format('Y-m-d'))
        )->getBody();

        return new Comic(
            $this->extractTitle($html),
            $this->extractDescription($html),
            $this->extractImageUrl($html),
            $this->extractSourceUrl($html)
        );
    }

    /**
     * Get a random date since the start date.
     *
     * @return \Carbon\Carbon
     */
    protected function randomDate(): Carbon
    {
        return Collection::make(
            Carbon::parse(self::START_DATE, 'America/Phoenix')->daysUntil(Carbon::now())
        )->random();
    }

    /**
     * Extract the comcic's title from the HTML.
     *
     * @param string $html
     *
     * @return string
     */
    protected function extractTitle(string $html): string
    {
        preg_match('/<meta\s*property="og:title"\s*content="(?<title>.*)"\s*\/>/', $html, $matches);

        return $matches['title'];
    }

    /**
     * Extract the comcic's description from the HTML.
     *
     * @param string $html
     *
     * @return string
     */
    protected function extractDescription(string $html): string
    {
        preg_match('/<meta\s*property="og:description"\s*content="(?<description>.*)"\s*\/>/sU', $html, $matches);

        return $matches['description'] ?? '';
    }

    /**
     * Extract the comic's image URL from the HTML.
     *
     * @param string $html
     *
     * @return string
     */
    protected function extractImageUrl(string $html): string
    {
        preg_match('/<meta\s*property="og:image"\s*content="(?<image_url>.*)"\s*\/>/', $html, $matches);

        return $matches['image_url'];
    }

    /**
     * Extract the comic's source URL from the HTML.
     *
     * @param string $html
     *
     * @return string
     */
    protected function extractSourceUrl(string $html): string
    {
        preg_match('/<meta\s*property="og:url"\s*content="(?<source_url>.*)"\s*\/>/', $html, $matches);

        return $matches['source_url'];
    }
}
<?php

namespace App\Http\Clients;

use App\Comic;
use GuzzleHttp\Client;
use SimpleXMLElement;

class MonkeyUserClient
{
    /** @var Client The Guzzle HTTP client */
    protected $client;

    /**
     * Create a new MonkeyUserClient object.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->client = new Client((array) array_replace_recursive([
            'base_uri' => 'https://www.monkeyuser.com',
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
        $xml = new SimpleXMLElement(
            (string) $this->client->get('feed.xml')->getBody()
        );

        $comic = $xml->channel->item[0];

        return new Comic(
            $comic->title,
            $this->extractAltText($comic),
            $this->extractImageUrl($comic),
            $comic->link
        );
    }

    /**
     * Extract the alt text from a comic XML object.
     *
     * @param \SimpleXMLElement $comic
     *
     * @return string
     */
    protected function extractAltText(SimpleXMLElement $comic): string
    {
        preg_match('/title="(?<alt_text>.+)"/U', $comic->description, $matches);

        return $matches['alt_text'];
    }

    /**
     * Extract the image URL from a comic XML object.
     *
     * @param \SimpleXMLElement $comic
     *
     * @return string
     */
    protected function extractImageUrl(SimpleXMLElement $comic): string
    {
        preg_match('/img\s+src="(?<image_url>.+)"/U', $comic->description, $matches);

        return $matches['image_url'];
    }
}
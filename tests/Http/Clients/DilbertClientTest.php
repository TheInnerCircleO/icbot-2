<?php

namespace Tests\Http\Clients;

use App\Http\Clients\DilbertClient;
use Carbon\Carbon;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

/** @covers \App\Http\Clients\DilbertClient */
class DilbertClientTest extends TestCase
{
    /** @test */
    public function it_can_fetch_the_latest_comic(): void
    {
        $comic = $this->mockDilbertClient()->latest();

        $this->assertEquals('Test comic; please ignore', $comic->title);
        $this->assertEquals('Some test description.\nAnd a second line.', $comic->altText);
        $this->assertEquals('http://assets.amuniversal.com/test', $comic->imageUrl);
        $this->assertEquals('http://dilbert.com/strip/1986-05-20', $comic->sourceUrl);
    }

    /** @test */
    public function it_can_fetch_a_comic_by_date(): void
    {
        $comic = $this->mockDilbertClient()->byDate(
            Carbon::parse('1986-05-20')
        );

        $this->assertEquals('Test comic; please ignore', $comic->title);
        $this->assertEquals('Some test description.\nAnd a second line.', $comic->altText);
        $this->assertEquals('http://assets.amuniversal.com/test', $comic->imageUrl);
        $this->assertEquals('http://dilbert.com/strip/1986-05-20', $comic->sourceUrl);
    }

    /** @test */
    public function it_can_fetch_a_random_comic(): void
    {
        $comic = $this->mockDilbertClient()->random();

        $this->assertEquals('Test comic; please ignore', $comic->title);
        $this->assertEquals('Some test description.\nAnd a second line.', $comic->altText);
        $this->assertEquals('http://assets.amuniversal.com/test', $comic->imageUrl);
        $this->assertEquals('http://dilbert.com/strip/1986-05-20', $comic->sourceUrl);
    }

    /** Create a mocked DilbertClient object. */
    protected function mockDilbertClient(?array $responses = null): DilbertClient
    {
        return new DilbertClient([
            'handler' => HandlerStack::create(
                new MockHandler($responses ?? [
                    new Response(200, [], file_get_contents(
                        $this->path('dilbert.html')
                    )),
                ])
            ),
        ]);
    }
}

<?php

namespace App\Tests\Controller;

use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class ApiControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testApiLandingPage(): void
    {
        $this->client->request('GET', '/api');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.api-title h1', 'JSON API Overview');
        $this->assertSelectorExists('.api-routes .api-route');
        $this->assertGreaterThanOrEqual(
            8,
            $this->client->getCrawler()->filter('.api-route')->count()
        );
    }

    public function testApiQuote(): void
    {
        $this->client->request('GET', '/api/quote');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array{quote:string,date:string,timestamp:string} $data */
        $data = json_decode($content, true);

        $this->assertArrayHasKey('quote', $data);
        $this->assertArrayHasKey('date', $data);
        $this->assertArrayHasKey('timestamp', $data);
    }

    public function testApiTime(): void
    {
        $this->client->request('GET', '/api/time');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array{current_time:string,current_date:string,timezone:string} $data */
        $data = json_decode($content, true);

        $this->assertEquals('Europe/Stockholm', $data['timezone']);
    }

    public function testApiDeck(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $deck = new DeckOfCards();
        $deck->shuffle();

        $session->set('deck', $deck->toArray());
        $this->client->getContainer()->set('session', $session);

        $this->client->request('GET', '/api/deck');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array{cards:array<int,array<string,string>>,remaining:int} $data */
        $data = json_decode($content, true);

        $this->assertCount(52, $data['cards']);
        $this->assertEquals(52, $data['remaining']);
    }

    public function testApiShuffle(): void
    {
        $this->client->request('POST', '/api/deck/shuffle');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array{cards:array<int,array<string,string>>,remaining:int} $data */
        $data = json_decode($content, true);

        $this->assertCount(52, $data['cards']);
        $this->assertEquals(52, $data['remaining']);
    }

    public function testApiDrawOneCard(): void
    {
        $this->client->request('POST', '/api/deck/shuffle');
        $this->client->request('POST', '/api/deck/draw');

        $this->assertResponseIsSuccessful();

        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array{drawn:array<int,array<string,string>>,remaining:int} $data */
        $data = json_decode($content, true);

        $this->assertCount(1, $data['drawn']);
        $this->assertEquals(51, $data['remaining']);
    }

    public function testApiDrawMultipleCards(): void
    {
        $this->client->request('POST', '/api/deck/shuffle');
        $this->client->request('POST', '/api/deck/draw/3');

        $this->assertResponseIsSuccessful();

        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array{drawn:array<int,array<string,string>>,remaining:int} $data */
        $data = json_decode($content, true);

        $this->assertCount(3, $data['drawn']);
        $this->assertEquals(49, $data['remaining']);
    }

    public function testApiLibraryBooks(): void
    {
        $this->client->request('GET', '/api/library/books');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array<int,array<string,mixed>> $data */
        $data = json_decode($content, true);

        $this->assertGreaterThanOrEqual(3, count($data));
    }

    public function testApiLibraryBookByIsbn(): void
    {
        $this->client->request('GET', '/api/library/book/9780446310789');

        $this->assertResponseIsSuccessful();

        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array{title:string} $data */
        $data = json_decode($content, true);

        $this->assertEquals('To Kill a Mockingbird', $data['title']);
    }

    public function testShowAllBooksJson(): void
    {
        $this->client->request('GET', '/library/books');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array<int,array<string,mixed>> $data */
        $data = json_decode($content, true);

        $this->assertGreaterThanOrEqual(3, count($data));
        $this->assertArrayHasKey('title', $data[0]);
    }

    public function testShowBookByIsbnJson(): void
    {
        $this->client->request('GET', '/library/book/9780446310789');

        $this->assertResponseIsSuccessful();

        $content = $this->client->getResponse()->getContent();
        $this->assertNotFalse($content);

        /** @var array{title:string} $data */
        $data = json_decode($content, true);

        $this->assertEquals('To Kill a Mockingbird', $data['title']);
    }
}

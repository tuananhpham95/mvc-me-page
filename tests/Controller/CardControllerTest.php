<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CardControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCardIndex(): void
    {
        $this->client->request('GET', '/card');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.card-landing h1', 'Card Game');
        $this->assertSelectorExists('pre');
        $this->assertSelectorExists('a[href$="/card/deck"]');
        $this->assertSelectorExists('a[href$="/card/deck/shuffle"]');
    }

    public function testSessionShowEmpty(): void
    {
        $this->client->request('GET', '/session');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('main h1', 'Session Contents');
        $this->assertSelectorTextContains('pre', '"empty"');
    }

    public function testSessionDelete(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $session->set('test', 'value');

        $this->client->getContainer()->set('session', $session);

        $this->client->request('GET', '/session/delete');

        $this->assertResponseRedirects('/session');
        $this->client->followRedirect();

        $this->assertSelectorExists('.alert-success');
        $this->assertSelectorTextContains('.alert-success', 'Session has been cleared.');
        $this->assertSelectorTextContains('pre', '"empty"');
    }

    public function testCardDeckNew(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $this->client->getContainer()->set('session', $session);

        $this->client->request('GET', '/card/deck');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('main h1', 'Deck of Cards');
        $this->assertSelectorTextContains('p', 'Number of cards remaining: 52');
        $this->assertGreaterThanOrEqual(
            52,
            $this->client->getCrawler()->filter('.card-deck img')->count()
        );
    }

    public function testCardDeckShuffle(): void
    {
        $this->client->request('GET', '/card/deck/shuffle');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('main h1', 'Deck of Cards');
        $this->assertSelectorTextContains('p', 'Number of cards remaining: 52');
        $this->assertGreaterThanOrEqual(
            52,
            $this->client->getCrawler()->filter('.card-deck img')->count()
        );
    }

    public function testCardDeckDrawOne(): void
    {
        $this->client->request('GET', '/card/deck/shuffle');
        $this->client->request('GET', '/card/deck/draw');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('main h1', 'Drawn Cards');
        $this->assertSelectorTextContains('p', 'Number of cards remaining: 51');
        $this->assertCount(
            1,
            $this->client->getCrawler()->filter('.card-deck img')
        );
    }

    public function testCardDeckDrawMultiple(): void
    {
        $this->client->request('GET', '/card/deck/shuffle');
        $this->client->request('GET', '/card/deck/draw/5');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('main h1', 'Drawn Cards');
        $this->assertSelectorTextContains('p', 'Number of cards remaining: 47');
        $this->assertCount(
            5,
            $this->client->getCrawler()->filter('.card-deck img')
        );
    }
}

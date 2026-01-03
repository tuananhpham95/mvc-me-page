<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class GameControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        // Mock session để kiểm soát trạng thái game
        $session = new Session(new MockArraySessionStorage());
        $this->client->getContainer()->set('session', $session);
    }

    public function testGameIndex(): void
    {
        $this->client->request('GET', '/game');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.hero h1', 'Game 21');
        $this->assertSelectorTextContains(
            '.intro-text',
            'Välkommen till kortspelet Game 21'
        );
        $this->assertSelectorExists('a:contains("Starta Spelet")');
        $this->assertSelectorExists('a:contains("Dokumentation")');
    }

    public function testGameDoc(): void
    {
        $this->client->request('GET', '/game/doc');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            '.documentation-container h1',
            'Game 21 Documentation'
        );

        $h2s = $this->client->getCrawler()->filter('h2');
        $this->assertGreaterThanOrEqual(3, $h2s->count());
        $this->assertStringContainsString('Flowchart', $h2s->eq(0)->text());
        $this->assertStringContainsString('Pseudocode', $h2s->eq(1)->text());
        $this->assertStringContainsString('Class Descriptions', $h2s->eq(2)->text());

        $this->assertSelectorExists('.class-list li');
    }

    public function testGamePlayInitial(): void
    {
        $this->client->request('GET', '/game/play');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.game-board h1', 'Game 21');
        $this->assertSelectorExists('form[action$="/game/bet"]');
        $this->assertSelectorExists('input[name="bet_amount"]');
        $this->assertSelectorExists('button:contains("Place Bet")');
    }

    public function testGameBet(): void
    {
        $this->client->request('POST', '/game/bet', ['bet_amount' => '10']);

        $this->assertResponseRedirects('/game/play');
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('button:contains("Draw Card")');
        $this->assertSelectorExists('button:contains("Stand")');
    }

    public function testGameDraw(): void
    {
        $this->client->request('POST', '/game/bet', ['bet_amount' => '10']);
        $this->client->request('POST', '/game/draw');

        $this->assertResponseRedirects('/game/play');
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThanOrEqual(
            2,
            $this->client->getCrawler()->filter('.card-deck img')->count()
        );
    }

    public function testGameStand(): void
    {
        $this->client->request('POST', '/game/bet', ['bet_amount' => '10']);
        $this->client->request('POST', '/game/stand');

        $this->assertResponseRedirects('/game/play');
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.game-result', 'Winner:');
        $this->assertSelectorExists('button:contains("New Game")');
    }

    public function testGameReset(): void
    {
        $this->client->request('POST', '/game/bet', ['bet_amount' => '10']);
        $this->client->request('POST', '/game/reset');

        $this->assertResponseRedirects('/game/play');
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[action$="/game/bet"]');
        $this->assertSelectorExists('button:contains("Place Bet")');
    }
}

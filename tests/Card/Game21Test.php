<?php

namespace App\Tests\Card;

use App\Card\Game21;
use App\Card\Card;
use PHPUnit\Framework\TestCase;

class Game21Test extends TestCase
{
    public function testConstruct(): void
    {
        $game = new Game21();
        $this->assertSame('betting', $game->getStatus());
        $this->assertSame(50, $game->getPot());
        $this->assertSame(100, $game->getPlayer()->getMoney());
        $this->assertSame(100, $game->getBank()->getMoney());
        $this->assertNull($game->getWinner());
        $this->assertNull($game->getCurrentBet());
        $this->assertCount(52, $game->getDeck()->getCards());
    }

    public function testDealInitialCard(): void
    {
        $game = new Game21();
        $game->dealInitialCard();
        $this->assertCount(1, $game->getPlayer()->getHand()->getCards());
        $this->assertCount(51, $game->getDeck()->getCards());
    }

    public function testPlaceBet(): void
    {
        $game = new Game21();
        $game->placeBet(20);
        $this->assertSame('playing', $game->getStatus());
        $this->assertSame(70, $game->getPot());
        $this->assertSame(80, $game->getPlayer()->getMoney());
        $this->assertSame(20, $game->getCurrentBet());
        $this->assertCount(1, $game->getPlayer()->getHand()->getCards());
    }

    public function testPlaceBetInvalid(): void
    {
        $this->expectException(\Exception::class);
        $game = new Game21();
        $game->placeBet(60); // Exceeds pot
    }

    public function testPlayerDrawBust(): void
    {
        $game = new Game21();
        $game->placeBet(10);

        $player = $game->getPlayer();
        // Clear hand safely
        $this->clearPlayerHand($player);

        $player->addCard(new Card('Hearts', 'K'));  // 13
        $player->addCard(new Card('Spades', 'Q'));  // 12 → sum = 25 > 21

        $game->playerDraw();

        $this->assertSame('finished', $game->getStatus());
        $this->assertSame('Bank', $game->getWinner());
    }

    public function testPlayerDrawTo21(): void
    {
        $game = new Game21();
        $player = $game->getPlayer();

        $this->clearPlayerHand($player);

        $game->placeBet(10);

        $player->addCard(new Card('hearts', 'Ace'));
        $player->addCard(new Card('spades', '7'));

        $game->playerDraw();

        $this->assertSame('finished', $game->getStatus(), "Player should reach 21");
    }


    public function testPlayerStandAndBankPlay(): void
    {
        $game = new Game21();
        $game->placeBet(20);

        $player = $game->getPlayer();
        $this->clearPlayerHand($player);

        $player->addCard(new Card('Hearts', '10'));
        $game->playerStand();

        $this->assertSame('finished', $game->getStatus());
        $this->assertNotNull($game->getWinner());
        $this->assertContains($game->getWinner(), ['Player', 'Bank']);
        $this->assertSame(50, $game->getPot());
    }

    // Helper to clear player's hand safely
    private function clearPlayerHand(\App\Card\Player $player): void
    {
        $hand = $player->getHand();
        $reflection = new \ReflectionClass($hand);
        $property = $reflection->getProperty('cards');
        $property->setAccessible(true);
        $property->setValue($hand, []);
    }
}

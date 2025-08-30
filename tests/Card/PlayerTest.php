<?php

namespace App\Tests\Card;

use App\Card\Card;
use App\Card\Player;
use App\Card\CardHand;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for the Player class.
 */
class PlayerTest extends TestCase
{
    /**
     * Tests the constructor and getter methods.
     */
    public function testConstructAndGetters(): void
    {
        $player = new Player('Test', 100);
        $this->assertSame('Test', $player->getName());
        $this->assertSame(100, $player->getMoney());
        $this->assertEmpty($player->getHand()->getCards());
        $this->assertFalse($player->hasStood());
    }

    /**
     * Tests adding a card and calculating score.
     */
    public function testAddCardAndScore(): void
    {
        $player = new Player('Test', 100);

        // Clear the hand first
        $reflection = new \ReflectionClass($player);
        $handProp = $reflection->getProperty('hand');
        $handProp->setAccessible(true);
        $handProp->setValue($player, new CardHand());

        $player->addCard(new Card('Hearts', 'A')); // 14
        $this->assertSame(14, $player->getScore());
    }




    /**
     * Tests score with multiple aces.
     */
    public function testScoreWithMultipleAces(): void
    {
        $player = new Player('Test', 100);

        $reflection = new \ReflectionClass($player);
        $handProp = $reflection->getProperty('hand');
        $handProp->setAccessible(true);
        $handProp->setValue($player, new CardHand());

        $player->addCard(new Card('Hearts', 'A')); // 14
        $player->addCard(new Card('Spades', 'A')); // 1
        $this->assertSame(15, $player->getScore());
    }



    /**
     * Tests the stand method.
     */
    public function testStand(): void
    {
        $player = new Player('Test', 100);
        $player->stand();
        $this->assertTrue($player->hasStood());
    }

    /**
     * Tests money operations.
     */
    public function testMoneyOperations(): void
    {
        $player = new Player('Test', 100);
        $player->addMoney(50);
        $this->assertSame(150, $player->getMoney());
        $player->deductMoney(30);
        $this->assertSame(120, $player->getMoney());
    }

    /**
     * Tests deducting too much money throws an exception.
     */
    public function testDeductMoneyException(): void
    {
        $this->expectException(\Exception::class);
        $player = new Player('Test', 10);
        $player->deductMoney(20);
    }

    /**
     * Tests toArray and fromArray methods.
     */
    public function testToArrayAndFromArray(): void
    {
        $player = new Player('Test', 100);
        $player->addCard(new Card('Hearts', 'Ace'));
        $data = $player->toArray();
        $newPlayer = Player::fromArray($data);
        $this->assertSame('Test', $newPlayer->getName());
        $this->assertSame(100, $newPlayer->getMoney());
        $this->assertCount(1, $newPlayer->getHand()->getCards());
    }
}
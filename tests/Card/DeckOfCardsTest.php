<?php

namespace App\Tests\Card;

use App\Card\DeckOfCards;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for the DeckOfCards class.
 */
class DeckOfCardsTest extends TestCase
{
    /**
     * Tests the constructor initializes a deck with 52 cards.
     */
    public function testConstruct(): void
    {
        $deck = new DeckOfCards();
        $this->assertCount(52, $deck->getCards());
    }

    /**
     * Tests drawing cards from the deck.
     */
    public function testDraw(): void
    {
        $deck = new DeckOfCards();
        $cards = $deck->draw(2);
        $this->assertCount(2, $cards);
        $this->assertCount(50, $deck->getCards());
    }

    /**
     * Tests drawing too many cards throws an exception.
     */
    public function testDrawTooManyCards(): void
    {
        $this->expectException(\Exception::class);
        $deck = new DeckOfCards();
        $deck->draw(53);
    }

    /**
     * Tests shuffling the deck changes card order.
     */
    public function testShuffle(): void
    {
        $deck = new DeckOfCards();
        $original = $deck->getCards();
        $deck->shuffle();
        $shuffled = $deck->getCards();
        $this->assertCount(52, $shuffled);
        $this->assertNotEquals($original, $shuffled);
    }

    /**
     * Tests getting sorted cards.
     */
    public function testGetSortedCards(): void
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $sorted = $deck->getSortedCards();
        $this->assertCount(52, $sorted);
        $this->assertSame('A of Hearts', $sorted[0]->getAsString());
    }

    /**
     * Tests toArray and fromArray methods.
     */
    public function testToArrayAndFromArray(): void
    {
        $deck = new DeckOfCards();
        $data = $deck->toArray();
        $newDeck = DeckOfCards::fromArray($data);
        $this->assertCount(52, $newDeck->getCards());
    }

    /**
     * Tests getCardCount method.
     */
    public function testGetCardCount(): void
    {
        $deck = new DeckOfCards();
        $this->assertSame(52, $deck->getCardCount());
        $deck->draw(5);
        $this->assertSame(47, $deck->getCardCount());
    }
}

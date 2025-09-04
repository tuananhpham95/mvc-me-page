<?php

namespace App\Tests\Card;

use App\Card\Card;
use App\Card\CardHand;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for the CardHand class.
 */
class CardHandTest extends TestCase
{
    /**
     * Tests adding a card and retrieving cards.
     */
    public function testAddCardAndGetCards(): void
    {
        $hand = new CardHand();
        $card = new Card('Hearts', 'Ace');
        $hand->addCard($card);
        $this->assertCount(1, $hand->getCards());
        $this->assertSame($card, $hand->getCards()[0]);
    }

    /**
     * Tests the toArray method.
     */
    public function testToArray(): void
    {
        $hand = new CardHand();
        $card = new Card('Hearts', 'Ace');
        $hand->addCard($card);
        $expected = [['suit' => 'Hearts', 'value' => 'Ace']];
        $this->assertSame($expected, $hand->toArray());
    }

    /**
     * Tests the fromArray method.
     */
    public function testFromArray(): void
    {
        $data = [['suit' => 'Hearts', 'value' => 'Ace']];
        $hand = CardHand::fromArray($data);
        $this->assertCount(1, $hand->getCards());
        $this->assertSame('Ace of Hearts', $hand->getCards()[0]->getAsString());
    }

    /**
     * Tests an empty hand.
     */
    public function testEmptyHand(): void
    {
        $hand = new CardHand();
        $this->assertEmpty($hand->getCards());
    }
}

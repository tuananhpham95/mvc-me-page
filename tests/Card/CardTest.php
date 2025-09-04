<?php

namespace App\Tests\Card;

use App\Card\Card;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for the Card class.
 */
class CardTest extends TestCase
{
    /**
     * Tests the constructor and getter methods.
     */
    public function testConstructAndGetters(): void
    {
        $card = new Card('Hearts', 'Ace');
        $this->assertSame('Hearts', $card->getSuit());
        $this->assertSame('Ace', $card->getValue());
        $this->assertSame('Ace of Hearts', $card->getAsString());
    }

    /**
     * Tests the toArray method.
     */
    public function testToArray(): void
    {
        $card = new Card('Hearts', 'Ace');
        $expected = ['suit' => 'Hearts', 'value' => 'Ace'];
        $this->assertSame($expected, $card->toArray());
    }

    /**
     * Tests the fromArray method.
     */
    public function testFromArray(): void
    {
        $data = ['suit' => 'Hearts', 'value' => 'Ace'];
        $card = Card::fromArray($data);
        $this->assertSame('Hearts', $card->getSuit());
        $this->assertSame('Ace', $card->getValue());
    }
}

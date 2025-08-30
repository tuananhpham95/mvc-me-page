<?php

namespace App\Tests\Card;

use App\Card\CardGraphic;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for the CardGraphic class.
 */
class CardGraphicTest extends TestCase
{
    /**
     * Tests the getSvgUrl method for different card values.
     */
    public function testGetSvgUrl(): void
    {
        $card = new CardGraphic('Hearts', 'Ace');
        $this->assertSame('https://upload.wikimedia.org/wikipedia/commons/d/d4/English_pattern_ace_of_hearts.svg', $card->getSvgUrl());

        $card = new CardGraphic('Spades', '10');
        $this->assertSame('https://upload.wikimedia.org/wikipedia/commons/d/da/English_pattern_10_of_spades.svg', $card->getSvgUrl());
    }

    /**
     * Tests the getAsString method with suit names.
     */
    public function testGetAsString(): void
    {
        $card = new CardGraphic('Hearts', 'A');
        $this->assertSame('A of Hearts', $card->getAsString());

        $card = new CardGraphic('Clubs', 'King');
        $this->assertSame('King of Clubs', $card->getAsString());
    }
}
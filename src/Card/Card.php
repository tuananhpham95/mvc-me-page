<?php

namespace App\Card;

/**
 * Represents a single playing card with a suit and value.
 */
class Card
{
    /**
     * The suit of the card (e.g., Hearts, Diamonds, Clubs, Spades).
     */
    protected string $suit;

    /**
     * The value of the card (e.g., Ace, 2, King).
     */
    protected string $value;

    /**
     * Constructs a new Card instance.
     *
     * @param string $suit  the suit of the card
     * @param string $value the value of the card
     */
    public function __construct(string $suit, string $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }

    /**
     * Gets the suit of the card.
     *
     * @return string the suit of the card
     */
    public function getSuit(): string
    {
        return $this->suit;
    }

    /**
     * Gets the value of the card.
     *
     * @return string the value of the card
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Gets a string representation of the card (e.g., "Ace of Hearts").
     *
     * @return string the card as a string
     */
    public function getAsString(): string
    {
        return "{$this->value} of {$this->suit}";
    }

    /**
     * Converts the card to an array representation.
     *
     * @return array<string, string> array with suit and value
     */
    public function toArray(): array
    {
        return [
            'suit' => $this->suit,
            'value' => $this->value,
        ];
    }

    /**
     * Creates a Card instance from an array.
     *
     * @param array<string, string> $data array with suit and value
     */
    public static function fromArray(array $data): self
    {
        return new self($data['suit'], $data['value']);
    }
}

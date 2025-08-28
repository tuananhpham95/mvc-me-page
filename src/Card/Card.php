<?php

namespace App\Card;

/**
 * Represents a single playing card with a suit and value.
 */
class Card
{
    /**
     * The suit of the card (e.g., Hearts, Diamonds, Clubs, Spades).
     *
     * @var string
     */
    protected string $suit;

    /**
     * The value of the card (e.g., Ace, 2, King).
     *
     * @var string
     */
    protected string $value;

    /**
     * Constructs a new Card instance.
     *
     * @param string $suit The suit of the card.
     * @param string $value The value of the card.
     */
    public function __construct(string $suit, string $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }

    /**
     * Gets the suit of the card.
     *
     * @return string The suit of the card.
     */
    public function getSuit(): string
    {
        return $this->suit;
    }

    /**
     * Gets the value of the card.
     *
     * @return string The value of the card.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Gets a string representation of the card (e.g., "Ace of Hearts").
     *
     * @return string The card as a string.
     */
    public function getAsString(): string
    {
        return "{$this->value} of {$this->suit}";
    }

    /**
     * Converts the card to an array representation.
     *
     * @return array<string, string> Array with suit and value.
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
     * @param array<string, string> $data Array with suit and value.
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self($data['suit'], $data['value']);
    }
}
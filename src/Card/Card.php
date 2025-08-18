<?php

namespace App\Card;

class Card
{
    protected string $suit;
    protected string $value;

    public function __construct(string $suit, string $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }

    public function getSuit(): string
    {
        return $this->suit;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getAsString(): string
    {
        return "{$this->value} {$this->suit}";
    }

    public function toArray(): array
    {
        return [
            'suit' => $this->suit,
            'value' => $this->value,
        ];
    }
}

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
        return "{$this->value} of {$this->suit}";
    }

    
    /**
     * @return array{suit: string, value: string}
     */
    public function toArray(): array
    {
        return [
            'suit' => $this->suit,
            'value' => $this->value,
        ];
    }

    /**
     * @param array{suit: string, value: string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data['suit'], $data['value']);
    }
}
<?php

namespace App\Card;

class CardHand
{
    private array $cards = [];

    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    public function getAsString(): string
    {
        return implode(', ', array_map(fn($card) => $card->getAsString(), $this->cards));
    }

    public function toArray(): array
    {
        return array_map(fn($card) => $card->toArray(), $this->cards);
    }
}
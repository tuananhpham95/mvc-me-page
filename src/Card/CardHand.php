<?php

namespace App\Card;

/**
 * @property Card[] $cards
 */
class CardHand
{
    /** @var Card[] */
    private array $cards = [];

    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * @return Card[]
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    public function getAsString(): string
    {
        return implode(', ', array_map(fn (Card $card) => $card->getAsString(), $this->cards));
    }

    /**
     * @return array<int, array{suit: string, value: string}>
     */
    public function toArray(): array
    {
        return array_map(fn (Card $card) => $card->toArray(), $this->cards);
    }

}

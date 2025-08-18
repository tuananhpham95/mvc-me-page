<?php

namespace App\Card;

class DeckOfCards
{
    private array $cards = [];
    private array $suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
    private array $values = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];

    public function __construct()
    {
        foreach ($this->suits as $suit) {
            foreach ($this->values as $value) {
                $this->cards[] = new CardGraphic($suit, $value);
            }
        }
        file_put_contents('/tmp/card_serialize.log', 'DeckOfCards constructed: ' . count($this->cards) . " cards\n", FILE_APPEND);
    }

    public function shuffle(): void
    {
        shuffle($this->cards);
        file_put_contents('/tmp/card_serialize.log', 'DeckOfCards shuffled: ' . count($this->cards) . " cards\n", FILE_APPEND);
    }

    public function draw(int $number = 1): array
    {
        if ($number < 1 || $number > count($this->cards)) {
            throw new \InvalidArgumentException("Cannot draw $number cards; only " . count($this->cards) . " cards remain.");
        }
        $drawnCards = array_splice($this->cards, 0, $number);
        file_put_contents('/tmp/card_serialize.log', 'DeckOfCards drew ' . $number . ' cards, remaining: ' . count($this->cards) . "\n", FILE_APPEND);
        return $drawnCards;
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    public function getCardCount(): int
    {
        return count($this->cards);
    }

    public function getSortedCards(): array
    {
        $sorted = $this->cards;
        usort($sorted, function ($a, $b) {
            $suitOrder = array_flip($this->suits);
            $valueOrder = array_flip($this->values);
            if ($a->getSuit() === $b->getSuit()) {
                return $valueOrder[$a->getValue()] <=> $valueOrder[$b->getValue()];
            }
            return $suitOrder[$a->getSuit()] <=> $suitOrder[$b->getSuit()];
        });
        return $sorted;
    }

    public function toArray(): array
    {
        $data = [
            'cards' => array_map(fn ($card) => $card->toArray(), $this->cards),
            'suits' => $this->suits,
            'values' => $this->values,
        ];
        file_put_contents('/tmp/card_serialize.log', 'DeckOfCards toArray: ' . json_encode($data, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
        return $data;
    }

    public static function fromArray(array $data): self
    {
        $deck = new self();
        $deck->cards = [];
        foreach ($data['cards'] as $cardData) {
            $deck->cards[] = new CardGraphic($cardData['suit'], $cardData['value']);
        }
        $deck->suits = $data['suits'] ?? ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
        $deck->values = $data['values'] ?? ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
        return $deck;
    }
}

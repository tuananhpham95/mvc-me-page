<?php

namespace App\Card;

/**
 * Class DeckOfCards.
 *
 * Represents a full deck of 52 playing cards using the CardGraphic class.
 * Provides functionality to shuffle, draw cards, get counts, sort,
 * and serialize/deserialize the deck to and from arrays.
 */
class DeckOfCards
{
    /**
     * All cards currently in the deck.
     *
     * @var CardGraphic[]
     */
    private array $cards = [];

    /**
     * The suits available in the deck.
     *
     * @var string[]
     */
    private array $suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];

    /**
     * The values available in the deck.
     *
     * @var string[]
     */
    private array $values = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];

    /**
     * Construct a new deck of 52 cards.
     *
     * Creates all combinations of suits and values as CardGraphic objects.
     */
    public function __construct()
    {
        foreach ($this->suits as $suit) {
            foreach ($this->values as $value) {
                $this->cards[] = new CardGraphic($suit, $value);
            }
        }
        file_put_contents('/tmp/card_serialize.log', 'DeckOfCards constructed: '.count($this->cards)." cards\n", FILE_APPEND);
    }

    /**
     * Shuffle the deck randomly.
     */
    public function shuffle(): void
    {
        shuffle($this->cards);
        file_put_contents('/tmp/card_serialize.log', 'DeckOfCards shuffled: '.count($this->cards)." cards\n", FILE_APPEND);
    }

    /**
     * Draw one or more cards from the top of the deck.
     *
     * @param int $number the number of cards to draw
     *
     * @return CardGraphic[] the drawn cards
     *
     * @throws \InvalidArgumentException if trying to draw more cards than remain in the deck
     */
    public function draw(int $number = 1): array
    {
        if ($number < 1 || $number > count($this->cards)) {
            throw new \InvalidArgumentException("Cannot draw $number cards; only ".count($this->cards).' cards remain.');
        }
        $drawnCards = array_splice($this->cards, 0, $number);
        file_put_contents('/tmp/card_serialize.log', 'DeckOfCards drew '.$number.' cards, remaining: '.count($this->cards)."\n", FILE_APPEND);

        return $drawnCards;
    }

    /**
     * Get all cards currently in the deck.
     *
     * @return CardGraphic[]
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * Get the number of cards currently left in the deck.
     *
     * @return int the number of remaining cards
     */
    public function getCardCount(): int
    {
        return count($this->cards);
    }

    /**
     * Get all cards in a sorted order by suit and value.
     *
     * @return CardGraphic[] the sorted cards
     */
    public function getSortedCards(): array
    {
        $sorted = $this->cards;
        usort($sorted, function (CardGraphic $a, CardGraphic $b) {
            $suitOrder = array_flip($this->suits);
            $valueOrder = array_flip($this->values);
            if ($a->getSuit() === $b->getSuit()) {
                return $valueOrder[$a->getValue()] <=> $valueOrder[$b->getValue()];
            }

            return $suitOrder[$a->getSuit()] <=> $suitOrder[$b->getSuit()];
        });

        return $sorted;
    }

    /**
     * Convert the deck into an array representation.
     *@return array{cards: array<int, array>, suits: string[], values: string[]}
     */
    public function toArray(): array
    {
        $data = [
            'cards' => array_map(fn (CardGraphic $card): array => [
                'suit' => $card->getSuit(),
                'value' => $card->getValue(),
            ], $this->cards),
            'suits' => $this->suits,
            'values' => $this->values,
        ];

        file_put_contents('/tmp/card_serialize.log', 'DeckOfCards toArray: '.json_encode($data, JSON_PRETTY_PRINT)."\n", FILE_APPEND);

        return $data;
    }

    /**
     * Recreate a DeckOfCards instance from array data.
     *
     * @param array{
     *     cards: array<int, array{suit: string, value: string}>,
     *     suits?: string[],
     *     values?: string[]
     * } $data Array of deck data
     *
     * @return self a new DeckOfCards instance
     */
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

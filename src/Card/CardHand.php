<?php

namespace App\Card;

/**
 * Class CardHand.
 *
 * Represents a hand of playing cards.
 * Provides methods to add cards, retrieve them,
 * convert the hand to a string, and serialize/deserialize.
 *
 * @property Card[] $cards The collection of cards in the hand.
 */
class CardHand
{
    /**
     * The cards currently in the hand.
     *
     * @var Card[]
     */
    private array $cards = [];

    /**
     * Add a card to the hand.
     *
     * @param Card $card the card to add
     */
    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    /**
     * Get all cards in the hand.
     *
     * @return Card[] array of Card objects
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * Get the hand as a comma-separated string.
     *
     * @return string A string representation of the cards, e.g. "10 of Hearts, Ace of Spades".
     */
    public function getAsString(): string
    {
        return implode(', ', array_map(fn (Card $card) => $card->getAsString(), $this->cards));
    }

    public function removeCard(Card $card): void
    {
        $index = array_search($card, $this->cards, true);
        if (false !== $index) {
            array_splice($this->cards, (int) $index, 1);
        }
    }

    /**
     * Convert the hand into an array format.
     *
     * @return array<int, array{suit: string, value: string}> array of card representations with literal keys
     */
    public function toArray(): array
    {
        return array_map(fn (Card $card): array => [
            'suit' => $card->getSuit(),
            'value' => $card->getValue(),
        ], $this->cards);
    }

    /**
     * Create a CardHand instance from an array of card data.
     *
     * @param array<int, array{suit: string, value: string}> $data array of cards with literal keys 'suit' and 'value'
     *
     * @return self a new CardHand instance populated with cards
     */
    public static function fromArray(array $data): self
    {
        $hand = new self();
        foreach ($data as $cardData) {
            $hand->addCard(Card::fromArray($cardData));
        }

        return $hand;
    }
}

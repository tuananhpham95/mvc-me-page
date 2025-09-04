<?php

namespace App\Card;

/**
 * Class Player.
 *
 * Represents a player in the Game21 card game.
 * Tracks the player's name, hand, score, money, and whether they have stood.
 */
class Player
{
    private string $name;
    private CardHand $hand;
    private bool $hasStood;
    private int $money;

    /**
     * Player constructor.
     *
     * @param string $name  player's name
     * @param int    $money initial amount of money (default 100)
     */
    public function __construct(string $name, int $money = 100)
    {
        $this->name = $name;
        $this->hand = new CardHand();
        $this->hasStood = false;
        $this->money = $money;
    }

    /** @return string Returns the player's name. */
    public function getName(): string
    {
        return $this->name;
    }

    /** @return CardHand Returns the player's hand of cards. */
    public function getHand(): CardHand
    {
        return $this->hand;
    }

    /**
     * Adds a card to the player's hand.
     *
     * @param Card $card the card to add
     */
    public function addCard(Card $card): void
    {
        $this->hand->addCard($card);
    }

    /** Marks the player as having stood. */
    public function stand(): void
    {
        $this->hasStood = true;
    }

    /** @return bool Returns true if the player has stood, false otherwise. */
    public function hasStood(): bool
    {
        return $this->hasStood;
    }

    /** @return int Returns the player's current money. */
    public function getMoney(): int
    {
        return $this->money;
    }

    /**
     * Adds money to the player's balance.
     *
     * @param int $amount amount to add
     */
    public function addMoney(int $amount): void
    {
        $this->money += $amount;
    }

    /**
     * Deducts money from the player's balance.
     *
     * @param int $amount amount to deduct
     *
     * @throws \Exception if the player does not have enough money
     */
    public function deductMoney(int $amount): void
    {
        if ($amount > $this->money) {
            throw new \Exception("Not enough money to deduct $amount");
        }
        $this->money -= $amount;
    }

    /**
     * Calculates the player's current score based on their hand.
     * Aces are counted as 14 if it does not exceed 21, otherwise as 1.
     *
     * @return int the player's score
     */
    public function getScore(): int
    {
        $score = 0;
        $aces = 0;
        foreach ($this->hand->getCards() as $card) {
            $value = $card->getValue();
            if ('A' === $value) {
                ++$aces;
            } elseif ('J' === $value) {
                $score += 11;
            } elseif ('Q' === $value) {
                $score += 12;
            } elseif ('K' === $value) {
                $score += 13;
            } else {
                $score += (int) $value;
            }
        }

        for ($i = 0; $i < $aces; ++$i) {
            if ($score + 14 <= 21) {
                $score += 14;
            } else {
                ++$score;
            }
        }

        return $score;
    }

    /**
     * Converts the player object to an array.
     *
     * @return array{
     *     name: string,
     *     hand: array<int, array{suit: string, value: string}>,
     *     hasStood: bool,
     *     money: int
     * }
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'hand' => $this->hand->toArray(),
            'hasStood' => $this->hasStood,
            'money' => $this->money,
        ];
    }

    /**
     * Creates a Player instance from an array.
     *
     * @param array{
     *     name: string,
     *     hand: array<int, array{suit: string, value: string}>,
     *     hasStood?: bool,
     *     money?: int
     * } $data
     *
     * @return self a new Player instance
     */
    public static function fromArray(array $data): self
    {
        $player = new self($data['name'], $data['money'] ?? 100);
        $player->hasStood = $data['hasStood'] ?? false;

        foreach ($data['hand'] as $cardData) {
            $player->hand->addCard(
                new CardGraphic($cardData['suit'], $cardData['value'])
            );
        }

        return $player;
    }
}

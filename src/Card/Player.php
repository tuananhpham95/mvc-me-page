<?php

namespace App\Card;

class Player
{
    private string $name;
    private CardHand $hand;
    private bool $hasStood;
    private int $money;

    public function __construct(string $name, int $money = 100)
    {
        $this->name = $name;
        $this->hand = new CardHand();
        $this->hasStood = false;
        $this->money = $money;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHand(): CardHand
    {
        return $this->hand;
    }

    public function addCard(Card $card): void
    {
        $this->hand->addCard($card);
    }

    public function stand(): void
    {
        $this->hasStood = true;
    }

    public function hasStood(): bool
    {
        return $this->hasStood;
    }

    public function getMoney(): int
    {
        return $this->money;
    }

    public function addMoney(int $amount): void
    {
        $this->money += $amount;
    }

    public function deductMoney(int $amount): void
    {
        $this->money -= $amount;
    }

    public function getScore(): int
    {
        $score = 0;
        $aces = 0;
        foreach ($this->hand->getCards() as $card) {
            $value = $card->getValue();
            if ($value === 'A') {
                $aces++;
            } elseif ($value === 'J') {
                $score += 11;
            } elseif ($value === 'Q') {
                $score += 12;
            } elseif ($value === 'K') {
                $score += 13;
            } else {
                $score += (int)$value;
            }
        }
        for ($i = 0; $i < $aces; $i++) {
            if ($score + 14 <= 21) {
                $score += 14; // Ace as 14
            } else {
                $score += 1; // Ace as 1
            }
        }
        return $score;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'hand' => $this->hand->toArray(),
            'hasStood' => $this->hasStood,
            'money' => $this->money,
        ];
    }

    public static function fromArray(array $data): self
    {
        $player = new self($data['name'], $data['money'] ?? 100);
        $player->hasStood = $data['hasStood'] ?? false;
        foreach ($data['hand'] as $cardData) {
            $player->hand->addCard(new CardGraphic($cardData['suit'], $cardData['value']));
        }
        return $player;
    }
}
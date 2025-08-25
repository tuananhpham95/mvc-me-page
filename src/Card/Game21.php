<?php

namespace App\Card;

use Exception;

class Game21
{
    private DeckOfCards $deck;
    private Player $player;
    private Player $bank;
    private string $status; // 'playing', 'betting', 'player_stand', 'finished'
    private ?string $winner;
    private int $pot;
    private ?int $currentBet;

    public function __construct()
    {
        $this->deck = new DeckOfCards();
        $this->deck->shuffle();
        $this->player = new Player('Player', 100);
        $this->bank = new Player('Bank', 100);
        $this->status = 'betting';
        $this->winner = null;
        $this->pot = 50; // Bank's initial contribution to the pot
        $this->currentBet = null;
    }

    public function getDeck(): DeckOfCards
    {
        return $this->deck;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getBank(): Player
    {
        return $this->bank;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getWinner(): ?string
    {
        return $this->winner;
    }

    public function getPot(): int
    {
        return $this->pot;
    }

    public function getCurrentBet(): ?int
    {
        return $this->currentBet;
    }

    public function dealInitialCard(): void
    {
        if ($this->status !== 'betting') {
            throw new Exception('Cannot deal initial card: game not in betting state.');
        }
        if ($this->deck->getCardCount() === 0) {
            throw new Exception('No cards left in the deck.');
        }
        $card = $this->deck->draw()[0];
        $this->player->addCard($card);
    }

    public function placeBet(int $amount): void
    {
        if ($this->status !== 'betting' || $amount > $this->pot || $amount > $this->player->getMoney() || $amount <= 0) {
            throw new Exception('Invalid bet: must be positive, less than pot, and within player funds.');
        }
        if ($this->deck->getCardCount() === 0) {
            throw new Exception('No cards left in the deck.');
        }
        $this->currentBet = $amount;
        $this->player->deductMoney($amount);
        $this->pot += $amount;
        $this->status = 'playing';
        $card = $this->deck->draw()[0];
        $this->player->addCard($card);
    }

    public function playerDraw(): void
    {
        if ($this->status !== 'playing') {
            throw new Exception('Cannot draw: game is not in playing state.');
        }
        if ($this->deck->getCardCount() === 0) {
            throw new Exception('No cards left in the deck.');
        }
        $card = $this->deck->draw()[0];
        $this->player->addCard($card);
        $score = $this->player->getScore();
        if ($score == 21) {
            $this->player->addMoney($this->currentBet * 2); // Award player's bet * 2
            $this->pot -= $this->currentBet; // Pot returns to bank's contribution (50)
            $this->status = 'finished';
            $this->winner = 'Player';
        } elseif ($score > 21) {
            $this->bank->addMoney($this->currentBet); // Bank gains player's bet
            $this->pot -= $this->currentBet; // Pot returns to bank's contribution (50)
            $this->status = 'finished';
            $this->winner = 'Bank';
        }
    }

    public function playerStand(): void
    {
        if ($this->status !== 'playing') {
            throw new Exception('Cannot stand: game is not in playing state.');
        }
        $this->player->stand();
        $this->status = 'player_stand';
        $this->playBank();
    }

    private function playBank(): void
    {
        while ($this->bank->getScore() < 17 && !$this->bank->hasStood()) {
            if ($this->deck->getCardCount() === 0) {
                $this->player->addMoney($this->currentBet * 2); // Award player's bet * 2
                $this->pot -= $this->currentBet; // Pot returns to bank's contribution (50)
                $this->status = 'finished';
                $this->winner = 'Player';
                return;
            }
            $card = $this->deck->draw()[0];
            $this->bank->addCard($card);
        }
        $this->bank->stand();
        $this->status = 'finished';
        $this->determineWinner();
    }

    private function determineWinner(): void
    {
        $playerScore = $this->player->getScore();
        $bankScore = $this->bank->getScore();

        if ($playerScore > 21) {
            $this->bank->addMoney($this->currentBet); // Bank gains player's bet
            $this->pot -= $this->currentBet; // Pot returns to bank's contribution (50)
            $this->winner = 'Bank';
        } elseif ($bankScore > 21) {
            $this->player->addMoney($this->currentBet * 2); // Award player's bet * 2
            $this->pot -= $this->currentBet; // Pot returns to bank's contribution (50)
            $this->winner = 'Player';
        } elseif ($bankScore == 21) {
            $this->bank->addMoney($this->currentBet); // Bank gains player's bet
            $this->pot -= $this->currentBet; // Pot returns to bank's contribution (50)
            $this->winner = 'Bank';
        } elseif ($playerScore > $bankScore) {
            $this->player->addMoney($this->currentBet * 2); // Award player's bet * 2
            $this->pot -= $this->currentBet; // Pot returns to bank's contribution (50)
            $this->winner = 'Player';
        } else {
            $this->bank->addMoney($this->currentBet); // Bank gains player's bet
            $this->pot -= $this->currentBet; // Pot returns to bank's contribution (50)
            $this->winner = 'Bank';
        }
    }

    public function reset(): void
    {
        $this->deck = new DeckOfCards();
        $this->deck->shuffle();
        $this->player = new Player('Player', $this->player->getMoney());
        $this->bank = new Player('Bank', $this->bank->getMoney());
        $this->status = 'betting';
        $this->winner = null;
        $this->pot = 50; // Bank's new contribution
        $this->currentBet = null;
    }

    public function toArray(): array
    {
        return [
            'deck' => $this->deck->toArray(),
            'player' => $this->player->toArray(),
            'bank' => $this->bank->toArray(),
            'status' => $this->status,
            'winner' => $this->winner,
            'pot' => $this->pot,
            'currentBet' => $this->currentBet,
        ];
    }

    public static function fromArray(array $data): self
    {
        $game = new self();
        $game->deck = DeckOfCards::fromArray($data['deck']);
        $game->player = Player::fromArray($data['player']);
        $game->bank = Player::fromArray($data['bank']);
        $game->status = $data['status'];
        $game->winner = $data['winner'] ?? null;
        $game->pot = $data['pot'] ?? 50;
        $game->currentBet = $data['currentBet'] ?? null;
        return $game;
    }
}
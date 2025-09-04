<?php

namespace App\Card;

/**
 * Manages the game21 logic, handling betting, card dealing, and winner determination.
 */
class Game21
{   /**
    * The deck of cards used in the game.
    */
    private DeckOfCards $deck;

    /**
     * The player participating in the game.
     */
    private Player $player;

    /**
     * The bank (dealer) participating in the game.
     */
    private Player $bank;

    /**
     * The current state of the game (betting, playing, player_stand, finished).
     */
    private string $status; // 'playing', 'betting', 'player_stand', 'finished'

    /**
     * The winner of the game, if determined.
     */
    private ?string $winner;

    /**
     * The current pot value.
     */
    private int $pot;

    /**
     * The current bet amount, if any.
     */
    private ?int $currentBet;

    /**
     * Constructs a new Tjugoett game instance with a shuffled deck and initial state.
     */
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

    /**
     * Returns the deck of cards used in the game.
     *
     * @return DeckOfCards the deck of cards
     */
    public function getDeck(): DeckOfCards
    {
        return $this->deck;
    }

    /**
     * Returns the player object.
     *
     * @return Player the player in the game
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * Returns the bank (dealer) object.
     *
     * @return Player the bank in the game
     */
    public function getBank(): Player
    {
        return $this->bank;
    }

    /**
     * Returns the current game status.
     *
     * @return string the current status (betting, playing, player_stand, finished)
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Returns the winner of the game, if determined.
     *
     * @return string|null the winner's name or null if not determined
     */
    public function getWinner(): ?string
    {
        return $this->winner;
    }

    /**
     * Returns the current pot value.
     *
     * @return int the total pot amount
     */
    public function getPot(): int
    {
        return $this->pot;
    }

    /**
     * Returns the current bet amount.
     *
     * @return int|null the bet amount or null if no bet is placed
     */
    public function getCurrentBet(): ?int
    {
        return $this->currentBet;
    }

    /**
     * Deals an initial card to the player in the betting state.
     *
     * @throws \Exception if the game is not in betting state or deck is empty
     */
    public function dealInitialCard(): void
    {
        if ('betting' !== $this->status) {
            throw new \Exception('Cannot deal initial card: game not in betting state.');
        }
        if (0 === $this->deck->getCardCount()) {
            throw new \Exception('No cards left in the deck.');
        }
        $card = $this->deck->draw()[0];
        $this->player->addCard($card);
    }

    /**
     * Places a bet, updates the pot, and deals a card to start the playing state.
     *
     * @param int $amount the bet amount
     *
     * @throws \Exception if the bet is invalid or deck is empty
     */
    public function placeBet(int $amount): void
    {
        if ('betting' !== $this->status || $amount > $this->pot || $amount > $this->player->getMoney() || $amount <= 0) {
            throw new \Exception('Invalid bet: must be positive, less than pot, and within player funds.');
        }
        if (0 === $this->deck->getCardCount()) {
            throw new \Exception('No cards left in the deck.');
        }
        $this->currentBet = $amount;
        $this->player->deductMoney($amount);
        $this->pot += $amount;
        $this->status = 'playing';
        $card = $this->deck->draw()[0];
        $this->player->addCard($card);
    }

    /**
     * Allows the player to draw a card and evaluates the score for win or bust.
     *
     * @throws \Exception if the game is not in playing state or deck is empty
     */
    public function playerDraw(): void
    {
        if ('playing' !== $this->status) {
            throw new \Exception('Cannot draw: game is not in playing state.');
        }
        if (0 === $this->deck->getCardCount()) {
            throw new \Exception('No cards left in the deck.');
        }
        $card = $this->deck->draw()[0];
        $this->player->addCard($card);
        $score = $this->player->getScore();
        if (21 == $score && null !== $this->currentBet) {
            $this->player->addMoney($this->currentBet * 2); // Award player's bet * 2
            $this->pot -= $this->currentBet;
            $this->status = 'finished';
            $this->winner = 'Player';
        } elseif ($score > 21 && null !== $this->currentBet) {
            $this->bank->addMoney($this->currentBet);
            $this->pot -= $this->currentBet;
            $this->status = 'finished';
            $this->winner = 'Bank';
        }
    }

    /**
     * Allows the player to stand, triggering the bank's turn.
     *
     * @throws \Exception if the game is not in playing state
     */
    public function playerStand(): void
    {
        if ('playing' !== $this->status) {
            throw new \Exception('Cannot stand: game is not in playing state.');
        }
        $this->player->stand();
        $this->status = 'player_stand';
        $this->playBank();
    }

    /**
     * Manages the bank's turn, drawing cards until score is at least 17.
     */
    private function playBank(): void
    {
        while ($this->bank->getScore() < 17 && !$this->bank->hasStood()) {
            if (0 === $this->deck->getCardCount() && null !== $this->currentBet) {
                $this->player->addMoney($this->currentBet * 2);
                $this->pot -= $this->currentBet;
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

    /**
     * Determines the winner by comparing player and bank scores.
     *
     * @throws \Exception if no bet has been placed
     */
    private function determineWinner(): void
    {
        $playerScore = $this->player->getScore();
        $bankScore = $this->bank->getScore();

        if (null === $this->currentBet) {
            throw new \Exception('No bet placed, cannot determine winner.');
        }

        if ($playerScore > 21) {
            $this->bank->addMoney($this->currentBet);
            $this->pot -= $this->currentBet;
            $this->winner = 'Bank';
        } elseif ($bankScore > 21) {
            $this->player->addMoney($this->currentBet * 2);
            $this->pot -= $this->currentBet;
            $this->winner = 'Player';
        } elseif (21 == $bankScore) {
            $this->bank->addMoney($this->currentBet);
            $this->pot -= $this->currentBet;
            $this->winner = 'Bank';
        } elseif ($playerScore > $bankScore) {
            $this->player->addMoney($this->currentBet * 2);
            $this->pot -= $this->currentBet;
            $this->winner = 'Player';
        } else {
            $this->bank->addMoney($this->currentBet);
            $this->pot -= $this->currentBet;
            $this->winner = 'Bank';
        }
    }

    /**
     * Resets the game to its initial state, keeping player and bank money.
     */
    public function reset(): void
    {
        $this->deck = new DeckOfCards();
        $this->deck->shuffle();
        $this->player = new Player('Player', $this->player->getMoney());
        $this->bank = new Player('Bank', $this->bank->getMoney());
        $this->status = 'betting';
        $this->winner = null;
        $this->pot = 50;
        $this->currentBet = null;
    }

    /**
     * Converts the game state to an array representation.
     *
     * @return array{
     *     deck: array<mixed>,
     *     player: array<mixed>,
     *     bank: array<mixed>,
     *     status: string,
     *     winner: string|null,
     *     pot: int,
     *     currentBet: int|null
     * } The game state as an array
     */
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

    /**
     * Creates a Game21 instance from an array representation.
     *
     * @param array{
     *     deck: array{cards: array<int, array{suit: string, value: string}>, suits?: array<string>, values?: array<string>},
     *     player: array{name: string, hand: array<int, array{suit: string, value: string}>, hasStood?: bool, money?: int},
     *     bank: array{name: string, hand: array<int, array{suit: string, value: string}>, hasStood?: bool, money?: int},
     *     status: string,
     *     winner?: string|null,
     *     pot?: int,
     *     currentBet?: int|null
     * } $data
     */
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

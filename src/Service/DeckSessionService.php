<?php

namespace App\Service;

use App\Card\DeckOfCards;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DeckSessionService
{
    private const SESSION_KEY = 'deck';

    public function getDeck(SessionInterface $session): DeckOfCards
    {
        /** @var array{cards?: array<int, array{suit: string, value: string}>} $deckData */
        $deckData = $session->get(self::SESSION_KEY, []);

        // Endast denna check behövs
        if (!isset($deckData['cards'])) {
            $deck = new DeckOfCards();
            $session->set(self::SESSION_KEY, $deck->toArray());
            return $deck;
        }

        return DeckOfCards::fromArray($deckData);
    }

    public function saveDeck(SessionInterface $session, DeckOfCards $deck): void
    {
        $session->set($this::SESSION_KEY, $deck->toArray());
    }
}

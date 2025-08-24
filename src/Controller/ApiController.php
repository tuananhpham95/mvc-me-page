<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use App\Card\Game21;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api_landing')]
    public function apiLanding(): Response
    {
        $routes = [
            ['route' => 'GET /api/quote', 'description' => 'Returns a random quote with date and timestamp.'],
            ['route' => 'GET /api/time', 'description' => 'Returns the current time, date, and timezone.'],
            ['route' => 'GET /api/deck', 'description' => 'Returns the sorted deck as JSON.'],
            ['route' => 'POST /api/deck/shuffle', 'description' => 'Shuffles the deck and returns it as JSON.'],
            ['route' => 'POST /api/deck/draw', 'description' => 'Draws one card and returns it with remaining count as JSON.'],
            ['route' => 'POST /api/deck/draw/{number}', 'description' => 'Draws specified number of cards and returns them with remaining count as JSON.'],
            ['route' => 'GET /api/game', 'description' => 'Returns the current state of the Tjugoett game as JSON.'],
        ];

        return $this->render('api/index.html.twig', [
            'routes' => $routes,
        ]);
    }

    #[Route('/api/quote', name: 'api_quote')]
    public function quote(): JsonResponse
    {
        $quotes = [
            "Det finns ingen bättre utbildning än motgångar. - Benjamin Disraeli",
            "Sjömannen ber inte om medvind, han lär sig segla. - Gustaf Lindborg",
            "Att misslyckas är bara ett annat sätt att lära sig hur man gör något rätt. - Marian Wright Edelman",
            "Du behöver inte bli någon du inte är för att bli bättre än du var. - Sidney Poitier",
            "Det är klokare att gå sin egen väg än att gå vilse i andras fotspår. - Okänd"
        ];

        $randomQuote = $quotes[array_rand($quotes)];
        $date = new \DateTime('now', new \DateTimeZone('Europe/Stockholm'));

        $data = [
            'quote' => $randomQuote,
            'date' => $date->format('Y-m-d'),
            'timestamp' => $date->format('Y-m-d H:i:s')
        ];

        return new JsonResponse($data);
    }

    #[Route('/api/time', name: 'api_time')]
    public function time(): JsonResponse
    {
        $date = new \DateTime('now', new \DateTimeZone('Europe/Stockholm'));

        $data = [
            'current_time' => $date->format('H:i:s'),
            'current_date' => $date->format('Y-m-d'),
            'timezone' => $date->getTimezone()->getName()
        ];

        return new JsonResponse($data);
    }

    #[Route('/api/deck', name: 'api_deck', methods: ['GET'])]
    public function apiDeck(SessionInterface $session): JsonResponse
    {
        $deck = $this->getDeckFromSession($session);
        $cards = array_map(fn($card) => $card->getAsString(), $deck->getSortedCards());
        return $this->json([
            'cards' => $cards,
            'remaining' => $deck->getCardCount(),
        ]);
    }

    #[Route('/api/deck/shuffle', name: 'api_deck_shuffle', methods: ['POST'])]
    public function apiShuffle(SessionInterface $session): JsonResponse
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('deck', $deck->toArray());
        $cards = array_map(fn($card) => $card->getAsString(), $deck->getCards());
        return $this->json([
            'cards' => $cards,
            'remaining' => $deck->getCardCount(),
        ]);
    }

    #[Route('/api/deck/draw', name: 'api_deck_draw', methods: ['POST'])]
    public function apiDraw(SessionInterface $session): JsonResponse
    {
        $deck = $this->getDeckFromSession($session);
        $drawnCards = $deck->draw();
        $session->set('deck', $deck->toArray());
        $cards = array_map(fn($card) => $card->getAsString(), $drawnCards);
        return $this->json([
            'drawn' => $cards,
            'remaining' => $deck->getCardCount(),
        ]);
    }

    #[Route('/api/deck/draw/{number<\d+>}', name: 'api_deck_draw_number', methods: ['POST'])]
    public function apiDrawNumber(SessionInterface $session, int $number): JsonResponse
    {
        $deck = $this->getDeckFromSession($session);
        $drawnCards = $deck->draw($number);
        $session->set('deck', $deck->toArray());
        $cards = array_map(fn($card) => $card->getAsString(), $drawnCards);
        return $this->json([
            'drawn' => $cards,
            'remaining' => $deck->getCardCount(),
        ]);
    }

    private function getDeckFromSession(SessionInterface $session): DeckOfCards
    {
        $deckData = $session->get('deck');
        if (!is_array($deckData) || empty($deckData['cards'])) {
            $deck = new DeckOfCards();
            $session->set('deck', $deck->toArray());
            return $deck;
        }
        return DeckOfCards::fromArray($deckData);
    }

    private function getGameFromSession(SessionInterface $session): Game21
    {
        $gameData = $session->get('game');
        if (!is_array($gameData) || empty($gameData['deck'])) {
            $game = new Game21();
            $session->set('game', $game->toArray());
            return $game;
        }
        return Game21::fromArray($gameData);
    }
}
<?php

namespace App\Controller;

use App\Card\Game21;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game', name: 'game_index')]
    public function index(): Response
    {
        return $this->render('game/index.html.twig');
    }

    #[Route('/game/doc', name: 'game_doc')]
    public function doc(): Response
    {
        $classDescriptions = [
            'Card' => 'Represents a single card with suit and value.',
            'CardGraphic' => 'Extends Card to provide SVG URLs for card rendering.',
            'CardHand' => 'Manages a collection of cards for a player or bank.',
            'DeckOfCards' => 'Manages a deck of 52 cards with shuffle and draw operations.',
            'Player' => 'Represents a player or bank with a hand, score calculation, and money for betting.',
            'Game21' => 'Manages the game state and logic for the 21 game.',
        ];

        return $this->render('game/doc.html.twig', [
            'classDescriptions' => $classDescriptions,
        ]);
    }

    #[Route('/game/play', name: 'game_play')]
    public function play(SessionInterface $session): Response
    {
        $game = $this->getGameFromSession($session);
        if ('betting' === $game->getStatus()) {
            $game->dealInitialCard();
            $session->set('game', $game->toArray());
        }
        $maxBet = min($game->getPot(), $game->getPlayer()->getMoney());

        return $this->render('game/play.html.twig', [
            'game' => $game,
            'maxBet' => $maxBet,
        ]);
    }

    #[Route('/game/bet', name: 'game_bet', methods: ['POST'])]
    public function bet(Request $request, SessionInterface $session): Response
    {
        $game = $this->getGameFromSession($session);
        try {
            $amount = (int) $request->request->get('bet_amount');
            $game->placeBet($amount);
            $session->set('game', $game->toArray());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('game_play');
    }

    #[Route('/game/draw', name: 'game_draw', methods: ['POST'])]
    public function draw(SessionInterface $session): Response
    {
        $game = $this->getGameFromSession($session);
        try {
            $game->playerDraw();
            $session->set('game', $game->toArray());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('game_play');
    }

    #[Route('/game/stand', name: 'game_stand', methods: ['POST'])]
    public function stand(SessionInterface $session): Response
    {
        $game = $this->getGameFromSession($session);
        try {
            $game->playerStand();
            $session->set('game', $game->toArray());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('game_play');
    }

    #[Route('/game/reset', name: 'game_reset', methods: ['POST'])]
    public function reset(SessionInterface $session): Response
    {
        $game = new Game21();
        $session->set('game', $game->toArray());

        return $this->redirectToRoute('game_play');
    }

    private function getGameFromSession(SessionInterface $session): Game21
    {
        $gameData = $session->get('game');

        if (
            !is_array($gameData)
            || !isset($gameData['deck'])
            || !is_array($gameData['deck'])
            || !isset($gameData['deck']['cards'])
            || !is_array($gameData['deck']['cards'])
        ) {
            $game = new Game21();
            $session->set('game', $game->toArray());

            return $game;
        }

        /**
         * @var array{
         *     deck: array{
         *         cards: array<int, array{suit: string, value: string}>,
         *         suits?: string[],
         *         values?: string[]
         *     },
         *     player: array{
         *         name: string,
         *         hand: array<int, array{suit: string, value: string}>,
         *         hasStood?: bool,
         *         money?: int
         *     },
         *     bank: array{
         *         name: string,
         *         hand: array<int, array{suit: string, value: string}>,
         *         hasStood?: bool,
         *         money?: int
         *     },
         *     status: string,
         *     winner?: string|null,
         *     pot?: int,
         *     currentBet?: int|null
         * } $gameData
         */
        return Game21::fromArray($gameData);
    }


}

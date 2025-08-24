<?php

namespace App\Controller;

use App\Card\Game21;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
            'Game21' => 'Manages the game state and logic for the Tjugoett game.',
        ];

        return $this->render('game/doc.html.twig', [
            'classDescriptions' => $classDescriptions,
        ]);
    }
}
<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    #[Route('/card', name: 'card_index')]
    public function index(): Response
    {
        $uml = <<<EOD
        UML Class Diagram:
        - Card (suit, value, getAsString())
        ^-- CardGraphic (extends Card, overrides getAsString(),provides getSvgUrl() for SVG images)
        - CardHand (cards: Card[], addCard(), getCards(), getAsString())
        - DeckOfCards (cards: Card[], suits, values, shuffle(), draw(), getCards(), getCardCount(), getSortedCards())
        Relationships:
        - CardHand composes Card (1-to-many)
        - DeckOfCards composes Card (1-to-many)
        - CardGraphic inherits from Card
        EOD;

        return $this->render('card/index.html.twig', [
            'uml' => $uml,
        ]);
    }
    #[Route('/session', name: 'session_show')]
    public function session(SessionInterface $session): Response
    {
        return $this->render('card/session.html.twig', [
            'session' => $session->all(),
        ]);
    }

    #[Route('/session/delete', name: 'session_delete')]
    public function sessionDelete(SessionInterface $session): Response
    {
        $session->clear();
        $this->addFlash('success', 'Session has been cleared.');
        return $this->redirectToRoute('session_show');
    }
    #[Route('/card/deck', name: 'card_deck')]
    public function deck(SessionInterface $session): Response
    {
        $deck = $this->getDeckFromSession($session);
        return $this->render('card/deck.html.twig', [
            'cards' => $deck->getSortedCards(),
            'cardCount' => $deck->getCardCount(),
        ]);
    }

    #[Route('/card/deck/shuffle', name: 'card_deck_shuffle')]
    public function shuffle(SessionInterface $session): Response
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('deck', $deck->toArray());
        return $this->render('card/deck.html.twig', [
            'cards' => $deck->getCards(),
            'cardCount' => $deck->getCardCount(),
        ]);
    }

    #[Route('/card/deck/draw', name: 'card_deck_draw')]
    public function draw(SessionInterface $session): Response
    {
        $deck = $this->getDeckFromSession($session);
        $drawnCards = $deck->draw();
        $session->set('deck', $deck->toArray());
        return $this->render('card/draw.html.twig', [
            'drawnCards' => $drawnCards,
            'cardCount' => $deck->getCardCount(),
        ]);
    }

    #[Route('/card/deck/draw/{number<\d+>}', name: 'card_deck_draw_number')]
    public function drawNumber(SessionInterface $session, int $number): Response
    {
        $deck = $this->getDeckFromSession($session);
        $drawnCards = $deck->draw($number);
        $session->set('deck', $deck->toArray());
        return $this->render('card/draw.html.twig', [
            'drawnCards' => $drawnCards,
            'cardCount' => $deck->getCardCount(),
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
}

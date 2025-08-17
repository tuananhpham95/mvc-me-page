<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
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
    
}
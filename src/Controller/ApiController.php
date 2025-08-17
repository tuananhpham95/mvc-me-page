<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api_landing')]
    public function apiLanding(): Response
    {
        return $this->render('landing.html.twig');
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
}
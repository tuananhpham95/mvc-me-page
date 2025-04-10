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
            "Att våga är att förlora fotfästet en stund. Att inte våga är att förlora sig själv. – Søren Kierkegaard",
            "Den som inte har mod att ta risker kommer inte att uppnå något i livet. – Muhammad Ali",
            "Allt du kan föreställa dig är verkligt. – Pablo Picasso"
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
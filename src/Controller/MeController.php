<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class MeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }

    #[Route('/report', name: 'report')]
    public function report(): Response
    {
        return $this->render('report.html.twig');
    }

    #[Route('/lucky', name: 'lucky')]
    public function lucky(): Response
    {
        $luckyValue = rand(1, 100);
        return $this->render('lucky.html.twig', ['luckyValue' => $luckyValue]);
    }
}

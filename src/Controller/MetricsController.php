<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetricsController extends AbstractController
{
    /**
     * Displays the metrics analysis page for code quality.
     *
     * @return Response The rendered metrics page
     */
    #[Route('/metrics', name: 'metrics_index')]
    public function index(): Response
    {
        return $this->render('metrics/index.html.twig');
    }
}

<?php
// src/Controller/ProjectController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/proj')]
class ProjectController extends AbstractController
{
    #[Route('/home', name: 'project_home')]
    public function home(): Response
    {
        return $this->render('proj/index.html.twig');
    }

    #[Route('/about', name: 'project_about')]
    public function about(): Response
    {
        return $this->render('proj/about.html.twig');
    }
}

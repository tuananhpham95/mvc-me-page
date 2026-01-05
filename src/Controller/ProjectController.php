<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Main Project Controller for the EduTracker application.
 *
 * Handles navigation to the core pages of the final project (kmom10/proj):
 * - Project home page
 * - About page with project description
 * - API testing page with interactive JSON endpoints
 * - Database information page (ORM explanation)
 *
 * All routes are prefixed with /proj
 */
#[Route('/proj')]
class ProjectController extends AbstractController
{
    /**
     * Project home page.
     *
     * Displays the main landing page of the EduTracker project,
     * introducing the purpose and features of the learning tracker.
     *
     * Route: GET /proj/home
     *
     * @return Response Rendered project home page
     */
    #[Route('/home', name: 'project_home')]
    public function home(): Response
    {
        return $this->render('proj/index.html.twig');
    }

    /**
     * About page for the project.
     *
     * Contains detailed description of the project, technologies used,
     * features implemented, and personal reflection.
     *
     * Route: GET /proj/about
     *
     * @return Response Rendered about page
     */
    #[Route('/about', name: 'project_about')]
    public function about(): Response
    {
        return $this->render('proj/about.html.twig');
    }

    /**
     * Interactive JSON API testing page.
     *
     * Displays all available JSON API endpoints with live testing forms:
     * - List all items
     * - Get single item
     * - Create new item (POST)
     * - Filter by status
     * - View statistics
     *
     * Allows testing the REST-like API directly in the browser.
     *
     * Route: GET /proj/api
     *
     * @return Response Rendered API documentation and testing page
     */
    #[Route('/api', name: 'project_api')]
    public function api(EntityManagerInterface $em): Response
    {
        $categories = $em->getRepository(\App\Entity\Category::class)->findAll();

        return $this->render('proj/api.html.twig', [
            'categories' => $categories,
        ]);
    }


    /**
     * About Database page (for optional ORM krav).
     *
     * Explains database structure, ORM usage with Doctrine,
     * table descriptions, relationships (if any), and comparison
     * between ORM and raw SQL from the database course.
     *
     * Includes ER diagram and reflection on advantages/disadvantages.
     *
     * Route: GET /proj/about/database
     *
     * @return Response Rendered database information page
     */
    #[Route('/about/database', name: 'proj_about_database')]
    public function aboutDatabase(): Response
    {
        return $this->render('proj/about/database.html.twig');
    }
}

<?php

namespace App\Controller;

use App\Entity\LearningItem;
use App\Form\LearningItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller handling CRUD operations for LearningItem entities in the EduTracker project.
 *
 * Provides web interface at /proj/edu for listing, creating, viewing, editing and deleting learning items.
 */
#[Route('/proj/edu')]
class LearningItemController extends AbstractController
{
    /**
     * Displays a list of all learning items, sorted by creation date (newest first).
     *
     * @param EntityManagerInterface $em Doctrine entity manager
     *
     * @return Response Rendered index page with list of items
     */
    #[Route('/', name: 'edu_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $items = $em->getRepository(LearningItem::class)
            ->findBy([], ['createdAt' => 'DESC']);

        return $this->render('proj/edu/index.html.twig', [
            'items' => $items,
        ]);
    }

    /**
     * Handles creation of a new learning item via form.
     *
     * On GET: displays empty form with default status 'learning'.
     * On POST: validates and persists new item if form is valid.
     *
     * @param Request                $request HTTP request
     * @param EntityManagerInterface $em      Doctrine entity manager
     *
     * @return Response Create form or redirect to index on success
     */
    #[Route('/create', name: 'edu_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $item = new LearningItem();
        $item->setStatus('learning'); // default status

        $form = $this->createForm(LearningItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->setCreatedAt(new \DateTime());
            $em->persist($item);
            $em->flush();

            $this->addFlash('success', 'Learning item created!');
            return $this->redirectToRoute('edu_index');
        }

        return $this->render('proj/edu/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays details of a single learning item.
     *
     * @param LearningItem $item The learning item entity (resolved by param converter)
     *
     * @return Response Rendered show page with item details
     */
    #[Route('/{id}', name: 'edu_show', methods: ['GET'])]
    public function show(LearningItem $item): Response
    {
        return $this->render('proj/edu/show.html.twig', [
            'item' => $item,
        ]);
    }

    /**
     * Handles editing an existing learning item via form.
     *
     * @param Request                $request HTTP request
     * @param LearningItem           $item    The item to edit (param converter)
     * @param EntityManagerInterface $em      Doctrine entity manager
     *
     * @return Response Edit form or redirect to index on success
     */
    #[Route('/{id}/edit', name: 'edu_edit')]
    public function edit(
        Request $request,
        LearningItem $item,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(LearningItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Learning item updated!');
            return $this->redirectToRoute('edu_index');
        }

        return $this->render('proj/edu/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $item,
        ]);
    }

    /**
     * Deletes a learning item if CSRF token is valid.
     *
     * @param Request                $request HTTP request containing CSRF token
     * @param LearningItem           $item    The item to delete
     * @param EntityManagerInterface $em      Doctrine entity manager
     *
     * @return Response Redirect to index page
     */
    #[Route('/{id}/delete', name: 'edu_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        LearningItem $item,
        EntityManagerInterface $em
    ): Response {
        $token = (string) $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $item->getId(), $token)) {
            $em->remove($item);
            $em->flush();
            $this->addFlash('success', 'Learning item deleted!');
        }

        return $this->redirectToRoute('edu_index');
    }
}
